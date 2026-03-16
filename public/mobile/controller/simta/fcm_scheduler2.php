<?php

class JadwalDosen{
	function __construct($nidn, $tipeDosen, $tglUjian, $jamUjian, $namaRuangan, $namaMhs, $judul, $token, $jnsUjian){
		$this->nidn       =$nidn;
		$this->tipeDosen  =$tipeDosen;
		$this->tglUjian   =$tglUjian;
		$this->jamUjian   =$jamUjian;
		$this->namaRuangan=$namaRuangan;
		$this->namaMhs    =$namaMhs;
		$this->judul      =$judul;
		$this->token      =$token;
		$this->jnsUjian   =$jnsUjian;
	}
}

$conn = new mysqli("localhost", "simta", "fikom123", "thesisapps_dev");

if ($conn->connect_error){
die("Koneksi Gagal: " . $conn->connect_error);
}

$sql = "
		SELECT
			vw_jadwal_dosen.*,
			tb_fcm_token.token_fcm 
		FROM
			vw_jadwal_dosen
			INNER JOIN tb_fcm_token ON vw_jadwal_dosen.nidn = tb_fcm_token.username 
		WHERE
			YEAR ( tgl_ujian ) = YEAR (
			CURDATE()) 
			AND MONTH ( tgl_ujian ) = MONTH (
			CURDATE()) 
			AND DAY ( tgl_ujian ) = DAY (
			CURDATE()) 
			AND token_fcm IS NOT NULL
		ORDER BY
			nidn;
	";

$result = $conn->query($sql);
$i=0;
while($record = $result->fetch_assoc()){
	$hasil[$i] = new JadwalDosen($record['nidn'], $record['tipe_dosen'], $record['tgl_ujian'], $record['jam_ujian'], $record['nama_ruangan'], $record['mahasiswa'],
		$record['judul'], $record['token_fcm'], $record['jns_ujian']);
	$i++;
}

$conn->close();

$linuxUser = "lutfi";
$pathPhp = '/usr/bin/php';
$pathFcmSched = '/home/lutfi/Documents/fcm_scheduler.php';
$pathFcmCli = '/home/lutfi/Documents/fcmCLI.php';

$tgl = intval(date("d")) + 1;

shell_exec('crontab -r');
shell_exec('(crontab -u '.$linuxUser.' -l ; echo "0 7 '.$tgl.' * * '.$pathPhp.' '.$pathFcmSched.'") | crontab -u '.$linuxUser.' -');

if (empty($hasil)) return;
foreach ($hasil as $jadwal) {
	shell_exec('(crontab -u '.$linuxUser.' -l ; echo "'.substr($jadwal->jamUjian, 3, 2).' '.substr($jadwal->jamUjian, 0, 2).
	' * * * '.$pathPhp.' '.$pathFcmCli.' token=\''.$jadwal->token.'\' judul=\''.$jadwal->jnsUjian.': '.$jadwal->namaMhs.'\' pesan=\'Judul: '.$jadwal->judul.'<br>Waktu Ujian: '.$jadwal->jamUjian.'<br>Ruangan: '.$jadwal->namaRuangan.'<br>Sebagai: '.$jadwal->tipeDosen.'\' ") | crontab -u '.$linuxUser.' -');
}

?>