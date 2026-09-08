<?php

namespace App\Services;

use Illuminate\Support\Collection;

class HonorariumAutomaticTypeSetupService
{
    const SCOPE_PROPOSAL = 'proposal';
    const SCOPE_FINAL_EXAM = 'ujian_meja';
    const SCOPE_COMBINED = 'gabungan';

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
        $scope = $this->expectedPaymentScope($examType, $finalProjectTypeCode);
        if ($scope === null) {
            return null;
        }

        if ($scope === self::SCOPE_COMBINED) {
            $name = 'Non Skripsi [proposal + Ujian Meja]';
        } elseif ($scope === self::SCOPE_PROPOSAL) {
            $name = 'Proposal';
        } elseif ($scope === self::SCOPE_FINAL_EXAM) {
            $name = 'Ujian Meja';
        } else {
            return null;
        }

        return $executive ? $name . ' Eksekutif' : $name;
    }

    public function expectedPaymentScope($examType, $finalProjectTypeCode)
    {
        $examType = $this->normalizeExamType($examType);
        $finalProjectTypeCode = strtoupper(trim((string) $finalProjectTypeCode));
        if ($examType === null || $finalProjectTypeCode === '') {
            return null;
        }

        if (strpos($finalProjectTypeCode, 'NS-') === 0) {
            return self::SCOPE_COMBINED;
        }

        if ($examType === 0) {
            return self::SCOPE_PROPOSAL;
        }

        return $examType === 2 ? self::SCOPE_FINAL_EXAM : null;
    }

    public function paymentScopeLabel($scope)
    {
        $labels = [
            self::SCOPE_PROPOSAL => 'Proposal',
            self::SCOPE_FINAL_EXAM => 'Ujian Meja',
            self::SCOPE_COMBINED => 'Proposal + Ujian Meja',
        ];

        return isset($labels[$scope]) ? $labels[$scope] : 'Belum diatur';
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

        $expectedScope = $this->expectedPaymentScope(
            $examType,
            $finalProjectType->kode_jenis_tugas_akhir
        );
        $expectedLabel = $this->paymentScopeLabel($expectedScope);

        if (strpos(strtoupper(trim((string) $finalProjectType->kode_jenis_tugas_akhir)), 'NS-') === 0
            && $hasCombinedConflict) {
            return $this->result(
                self::STATUS_COMBINED_CONFLICT,
                'Non-Skripsi memiliki record Proposal dan Ujian Akhir; tentukan satu pembayaran gabungan secara manual.',
                $expectedLabel,
                null,
                $expectedScope
            );
        }

        $finalProjectTypeId = (int) $finalProjectType->jenis_tugas_akhir_id;
        $matchingMasters = $masterPayments->filter(function ($master) use ($expectedScope, $executive, $finalProjectTypeId) {
            $typeIds = collect((array) $master->jenis_tugas_akhir_ids)->map(function ($id) {
                return (int) $id;
            });

            return isset($master->cakupan_ujian)
                && trim((string) $master->cakupan_ujian) === $expectedScope
                && (int) $master->untuk_mahasiswa_eksekutif === ($executive ? 1 : 0)
                && $typeIds->contains($finalProjectTypeId);
        })->values();

        if ($matchingMasters->isEmpty()) {
            return $this->result(
                self::STATUS_MASTER_MISSING,
                'Master pembayaran belum sesuai dengan cakupan ujian, kelas, dan Jenis TA.',
                $expectedLabel,
                null,
                $expectedScope
            );
        }

        if ($matchingMasters->count() > 1) {
            return $this->result(
                self::STATUS_MASTER_AMBIGUOUS,
                'Lebih dari satu master pembayaran cocok; rapikan master terlebih dahulu.',
                $expectedLabel,
                null,
                $expectedScope
            );
        }

        $matchingMaster = $matchingMasters->first();

        return $this->result(
            self::STATUS_READY,
            'Siap diterapkan otomatis.',
            $matchingMaster->name,
            (int) $matchingMaster->id_honorarium,
            $expectedScope
        );
    }

    protected function result($status, $message, $expectedName = null, $masterPaymentId = null, $expectedScope = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'expected_payment_name' => $expectedName,
            'master_payment_id' => $masterPaymentId,
            'expected_payment_scope' => $expectedScope,
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
