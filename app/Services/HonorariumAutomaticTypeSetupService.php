<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HonorariumAutomaticTypeSetupService
{
    const STATUS_READY = 'ready';
    const STATUS_CONFIGURED = 'configured';
    const STATUS_PROTECTED = 'protected';
    const STATUS_MISSING_FINAL_PROJECT_TYPE = 'missing_final_project_type';
    const STATUS_INVALID_EXAM_TYPE = 'invalid_exam_type';
    const STATUS_COMBINED_CONFLICT = 'combined_conflict';
    const STATUS_MASTER_MISSING = 'master_missing';
    const STATUS_MASTER_AMBIGUOUS = 'master_ambiguous';

    public function buildPlan(
        Collection $honorariums,
        Collection $finalProjectTypesByNim,
        Collection $executiveStudents,
        Collection $masterPayments,
        Collection $combinedConflicts
    ) {
        $summary = [
            self::STATUS_READY => 0,
            self::STATUS_CONFIGURED => 0,
            self::STATUS_PROTECTED => 0,
            self::STATUS_MISSING_FINAL_PROJECT_TYPE => 0,
            self::STATUS_INVALID_EXAM_TYPE => 0,
            self::STATUS_COMBINED_CONFLICT => 0,
            self::STATUS_MASTER_MISSING => 0,
            self::STATUS_MASTER_AMBIGUOUS => 0,
        ];
        $rows = collect();

        foreach ($honorariums as $honorarium) {
            $evaluation = $this->evaluate(
                $honorarium,
                $finalProjectTypesByNim->get($honorarium->C_NPM),
                $executiveStudents->has($honorarium->C_NPM),
                $masterPayments,
                $combinedConflicts->has($honorarium->C_NPM)
            );
            $rows->put((int) $honorarium->id, $evaluation);
            $summary[$evaluation['status']]++;
        }

        $blockingStatuses = [
            self::STATUS_MISSING_FINAL_PROJECT_TYPE,
            self::STATUS_INVALID_EXAM_TYPE,
            self::STATUS_COMBINED_CONFLICT,
            self::STATUS_MASTER_MISSING,
            self::STATUS_MASTER_AMBIGUOUS,
        ];
        $blockingCount = collect($blockingStatuses)->sum(function ($status) use ($summary) {
            return $summary[$status];
        });

        return [
            'rows' => $rows,
            'summary' => $summary,
            'ready_count' => $summary[self::STATUS_READY],
            'skipped_count' => $summary[self::STATUS_CONFIGURED] + $summary[self::STATUS_PROTECTED],
            'blocking_count' => $blockingCount,
            'can_apply' => $summary[self::STATUS_READY] > 0 && $blockingCount === 0,
        ];
    }

    public function expectedPaymentName($examType, $finalProjectTypeCode, $executive)
    {
        $examType = $this->normalizeExamType($examType);
        $finalProjectTypeCode = strtoupper(trim((string) $finalProjectTypeCode));
        if ($examType === null || $finalProjectTypeCode === '') {
            return null;
        }

        if (strpos($finalProjectTypeCode, 'NS-') === 0) {
            $name = 'Non Skripsi [proposal + Ujian Meja]';
        } elseif ($examType === 0) {
            $name = 'Proposal';
        } elseif ($examType === 2) {
            $name = 'Ujian Meja';
        } else {
            return null;
        }

        return $executive ? $name . ' Eksekutif' : $name;
    }

    public function needsTypeAssignment($honorarium)
    {
        return empty($honorarium->tipe_ujian)
            || in_array(trim((string) $honorarium->tipe_ujian), ['0', '2'], true);
    }

    public function hasPaidRole($honorarium)
    {
        foreach ($this->roles() as $role => $statusColumn) {
            if (trim((string) $honorarium->{$role}) !== ''
                && (int) $honorarium->{$statusColumn} === 3) {
                return true;
            }
        }

        return false;
    }

    protected function evaluate($honorarium, $finalProjectType, $executive, Collection $masterPayments, $hasCombinedConflict)
    {
        if ($this->hasPaidRole($honorarium)) {
            return $this->result(
                self::STATUS_PROTECTED,
                'Sudah memiliki pembayaran dan tidak boleh diubah.'
            );
        }

        if (!$this->needsTypeAssignment($honorarium)) {
            return $this->result(
                self::STATUS_CONFIGURED,
                'Sudah ditetapkan dan tidak diubah oleh setup otomatis.'
            );
        }

        if (!$finalProjectType || empty($finalProjectType->kode_jenis_tugas_akhir)) {
            return $this->result(
                self::STATUS_MISSING_FINAL_PROJECT_TYPE,
                'Jenis tugas akhir belum ditetapkan.'
            );
        }

        $examType = $this->normalizeExamType($honorarium->exam_type);
        if ($examType === null) {
            return $this->result(
                self::STATUS_INVALID_EXAM_TYPE,
                'Sumber ujian tidak valid; harus Proposal atau Ujian Akhir.'
            );
        }

        $expectedName = $this->expectedPaymentName(
            $examType,
            $finalProjectType->kode_jenis_tugas_akhir,
            $executive
        );

        if (strpos(strtoupper(trim((string) $finalProjectType->kode_jenis_tugas_akhir)), 'NS-') === 0
            && $hasCombinedConflict) {
            return $this->result(
                self::STATUS_COMBINED_CONFLICT,
                'Non-Skripsi memiliki record Proposal dan Ujian Akhir; tentukan satu pembayaran gabungan secara manual.',
                $expectedName
            );
        }

        $finalProjectTypeId = (int) $finalProjectType->jenis_tugas_akhir_id;
        $matchingMasters = $masterPayments->filter(function ($master) use ($expectedName, $executive, $finalProjectTypeId) {
            $typeIds = collect((array) $master->jenis_tugas_akhir_ids)->map(function ($id) {
                return (int) $id;
            });

            return $this->normalizeName($master->name) === $this->normalizeName($expectedName)
                && (int) $master->untuk_mahasiswa_eksekutif === ($executive ? 1 : 0)
                && $typeIds->contains($finalProjectTypeId);
        })->values();

        if ($matchingMasters->isEmpty()) {
            return $this->result(
                self::STATUS_MASTER_MISSING,
                'Master pembayaran belum sesuai dengan jenis ujian, kelas, dan Jenis TA.',
                $expectedName
            );
        }

        if ($matchingMasters->count() > 1) {
            return $this->result(
                self::STATUS_MASTER_AMBIGUOUS,
                'Lebih dari satu master pembayaran cocok; rapikan master terlebih dahulu.',
                $expectedName
            );
        }

        return $this->result(
            self::STATUS_READY,
            'Siap diterapkan otomatis.',
            $expectedName,
            (int) $matchingMasters->first()->id_honorarium
        );
    }

    protected function result($status, $message, $expectedName = null, $masterPaymentId = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'expected_payment_name' => $expectedName,
            'master_payment_id' => $masterPaymentId,
        ];
    }

    protected function normalizeExamType($examType)
    {
        if ($examType === 0 || $examType === '0') {
            return 0;
        }
        if ($examType === 2 || $examType === '2') {
            return 2;
        }

        return null;
    }

    protected function normalizeName($name)
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $name)));
    }

    protected function roles()
    {
        return [
            'KS' => 'KS_Stat',
            'PU' => 'PU_Stat',
            'PP' => 'PP_Stat',
            'P1' => 'P1_Stat',
            'P2' => 'P2_Stat',
            'P3' => 'P3_Stat',
        ];
    }
}
