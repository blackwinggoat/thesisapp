<?php

namespace App\Services;

class SiakadIpkService
{
    public function syncByNim($nim)
    {
        $nim = trim((string) $nim);
        if (!preg_match('/^[A-Za-z0-9]{5,20}$/', $nim)) {
            return $this->failure('NIM tidak valid untuk sinkronisasi IPK.');
        }

        $baseUrl = trim((string) config('services.siakad_ipk.base_url', ''));
        $apiKey = trim((string) config('services.siakad_ipk.api_key', ''));
        $endpoint = trim((string) config('services.siakad_ipk.endpoint', 'mhs-nilai'));
        $timeout = max(5, min(60, (int) config('services.siakad_ipk.timeout', 20)));

        if ($baseUrl === '' || $apiKey === '') {
            return $this->failure('Konfigurasi API SIAKAD untuk IPK belum lengkap.');
        }

        $separator = strpos($baseUrl, '?') === false ? '?' : '&';
        $url = $baseUrl . $separator . http_build_query([
            'endpoint' => $endpoint,
            'nim' => $nim,
        ], '', '&', PHP_QUERY_RFC3986);

        $curl = curl_init($url);
        if ($curl === false) {
            return $this->failure('Layanan cURL server tidak tersedia.');
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-API-Key: ' . $apiKey,
                'User-Agent: ThesisAppsFikom/1.0',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $body = curl_exec($curl);
        $error = curl_error($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($body === false || $status < 200 || $status >= 300) {
            return $this->failure($error !== ''
                ? 'Koneksi ke SIAKAD gagal.'
                : 'SIAKAD mengembalikan HTTP ' . $status . '.');
        }

        $payload = json_decode($body, true);
        if (!is_array($payload) || ($payload['status'] ?? null) !== 'success') {
            return $this->failure('Respons nilai dari SIAKAD tidak dapat digunakan.');
        }

        $calculation = self::calculateFromRows($payload['data'] ?? []);
        if (!$calculation['ok']) {
            return $this->failure($calculation['message']);
        }

        return array_merge($calculation, [
            'source' => 'SIAKAD mhs-nilai',
        ]);
    }

    public static function calculateFromRows($rows)
    {
        if (!is_array($rows)) {
            return self::calculationFailure('Data nilai SIAKAD tidak valid.');
        }

        $totalSks = 0;
        $totalMutu = 0.0;
        $courseCount = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $sks = $row['sks'] ?? null;
            $nilaiMutu = $row['nilai_mutu'] ?? null;
            if (!is_numeric($sks) || !is_numeric($nilaiMutu)) {
                continue;
            }

            $sks = (int) $sks;
            $nilaiMutu = (float) $nilaiMutu;
            if ($sks <= 0 || $nilaiMutu < 0 || $nilaiMutu > 4) {
                continue;
            }

            $totalSks += $sks;
            $totalMutu += $sks * $nilaiMutu;
            $courseCount++;
        }

        if ($totalSks <= 0 || $courseCount === 0) {
            return self::calculationFailure('SIAKAD belum memiliki nilai akhir aktif yang dapat dihitung.');
        }

        return [
            'ok' => true,
            'ipk' => round($totalMutu / $totalSks, 2),
            'total_sks' => $totalSks,
            'course_count' => $courseCount,
        ];
    }

    protected function failure($message)
    {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }

    protected static function calculationFailure($message)
    {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
}
