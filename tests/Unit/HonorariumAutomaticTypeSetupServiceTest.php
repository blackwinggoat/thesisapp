<?php

namespace Tests\Unit;

use App\Services\HonorariumAutomaticTypeSetupService;
use PHPUnit\Framework\TestCase;

class HonorariumAutomaticTypeSetupServiceTest extends TestCase
{
    public function testRegularProposalIsMappedByExamTypeClassAndFinalProjectType()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(100, 'Honor Proposal Reguler', false, [10], 'proposal')]),
            collect(),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame(1, $plan['ready_count']);
        $this->assertSame('Honor Proposal Reguler', $plan['rows']->get(1)['expected_payment_name']);
        $this->assertSame('proposal', $plan['rows']->get(1)['expected_payment_scope']);
        $this->assertSame(100, $plan['rows']->get(1)['master_payment_id']);
    }

    public function testExecutiveFinalExamUsesExecutiveFinalExamMaster()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 2)]),
            collect(['1301' => $this->finalProjectType(11, 'TA-SK')]),
            collect(['1301' => 0]),
            collect([$this->master(101, 'Ujian Meja Eksekutif', true, [11], 'ujian_meja')]),
            collect(['1301' => true]),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame('Ujian Meja Eksekutif', $plan['rows']->get(1)['expected_payment_name']);
    }

    public function testTaFinalExamWithoutProposalDecreeUsesCombinedPayment()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 2)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(102, 'Proposal + Ujian Meja', false, [10], 'gabungan')]),
            collect(),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame('gabungan', $plan['rows']->get(1)['expected_payment_scope']);
        $this->assertSame(102, $plan['rows']->get(1)['master_payment_id']);
    }

    public function testNonSkripsiFinalExamWithProposalDecreeUsesFinalExamPayment()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 2)]),
            collect(['1301' => $this->finalProjectType(20, 'NS-KT')]),
            collect(),
            collect([$this->master(103, 'Ujian Meja', false, [20], 'ujian_meja')]),
            collect(['1301' => true]),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame('ujian_meja', $plan['rows']->get(1)['expected_payment_scope']);
        $this->assertSame(103, $plan['rows']->get(1)['master_payment_id']);
    }

    public function testProposalExamUsesProposalPaymentRegardlessOfFinalProjectCode()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(20, 'NS-AI')]),
            collect(),
            collect([$this->master(104, 'Proposal', false, [20], 'proposal')]),
            collect(['1301' => true]),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame('proposal', $plan['rows']->get(1)['expected_payment_scope']);
        $this->assertSame(104, $plan['rows']->get(1)['master_payment_id']);
    }

    public function testFinalExamWithoutProposalDecreeAndSeparateProposalHonorariumIsBlocked()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 2)]),
            collect(['1301' => $this->finalProjectType(20, 'TA-SM')]),
            collect(),
            collect([$this->master(200, 'Proposal + Ujian Meja', false, [20], 'gabungan')]),
            collect(),
            collect(['1301' => 0])
        );

        $this->assertFalse($plan['can_apply']);
        $this->assertSame(1, $plan['blocking_count']);
        $this->assertSame(
            HonorariumAutomaticTypeSetupService::STATUS_COMBINED_CONFLICT,
            $plan['rows']->get(1)['status']
        );
    }

    public function testInvalidExamTypeIsNotSilentlyTreatedAsProposal()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, null)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(100, 'Proposal', false, [10], 'proposal')]),
            collect(),
            collect()
        );

        $this->assertSame(
            HonorariumAutomaticTypeSetupService::STATUS_INVALID_EXAM_TYPE,
            $plan['rows']->get(1)['status']
        );
    }

    public function testMasterMustExplicitlySupportTheFinalProjectType()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(100, 'Proposal', false, [], 'proposal')]),
            collect(),
            collect()
        );

        $this->assertSame(
            HonorariumAutomaticTypeSetupService::STATUS_MASTER_MISSING,
            $plan['rows']->get(1)['status']
        );
    }

    public function testOnlyTheMasterLinkedToTheSelectedFinalProjectTypeMatches()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([
                $this->master(100, 'Proposal A', false, [10], 'proposal'),
                $this->master(101, 'Proposal B', false, [11], 'proposal'),
            ]),
            collect(),
            collect()
        );

        $this->assertTrue($plan['can_apply']);
        $this->assertSame(100, $plan['rows']->get(1)['master_payment_id']);
    }

    public function testDuplicateMatchingMastersBlockTheWholePlan()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([
                $this->master(100, 'Proposal A', false, [10], 'proposal'),
                $this->master(101, 'Proposal B', false, [10], 'proposal'),
            ]),
            collect(),
            collect()
        );

        $this->assertFalse($plan['can_apply']);
        $this->assertSame(
            HonorariumAutomaticTypeSetupService::STATUS_MASTER_AMBIGUOUS,
            $plan['rows']->get(1)['status']
        );
    }

    public function testOneBlockingRowPreventsApplyingOtherwiseReadyRows()
    {
        $plan = $this->service()->buildPlan(
            collect([
                $this->honorarium(1, 0, '1301'),
                $this->honorarium(2, 0, '1302'),
            ]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(100, 'Proposal', false, [10], 'proposal')]),
            collect(),
            collect()
        );

        $this->assertSame(1, $plan['ready_count']);
        $this->assertSame(1, $plan['blocking_count']);
        $this->assertFalse($plan['can_apply']);
    }

    public function testMasterWithWrongExamScopeDoesNotMatchEvenWhenItsNameLooksCorrect()
    {
        $plan = $this->service()->buildPlan(
            collect([$this->honorarium(1, 0)]),
            collect(['1301' => $this->finalProjectType(10, 'TA-SM')]),
            collect(),
            collect([$this->master(100, 'Proposal', false, [10], 'ujian_meja')]),
            collect(),
            collect()
        );

        $this->assertFalse($plan['can_apply']);
        $this->assertSame(
            HonorariumAutomaticTypeSetupService::STATUS_MASTER_MISSING,
            $plan['rows']->get(1)['status']
        );
    }

    private function service()
    {
        return new HonorariumAutomaticTypeSetupService;
    }

    private function honorarium($id, $examType, $nim = '1301')
    {
        return (object) [
            'id' => $id,
            'C_NPM' => $nim,
            'exam_type' => $examType,
            'tipe_ujian' => '0',
            'KS' => 'D1',
            'KS_Stat' => 0,
            'PU' => 'D2',
            'PU_Stat' => 0,
            'PP' => 'D3',
            'PP_Stat' => 0,
            'P1' => 'D4',
            'P1_Stat' => 0,
            'P2' => 'D5',
            'P2_Stat' => 0,
            'P3' => '',
            'P3_Stat' => 0,
        ];
    }

    private function finalProjectType($id, $code)
    {
        return (object) [
            'jenis_tugas_akhir_id' => $id,
            'kode_jenis_tugas_akhir' => $code,
        ];
    }

    private function master($id, $name, $executive, array $finalProjectTypeIds, $scope)
    {
        return (object) [
            'id_honorarium' => $id,
            'name' => $name,
            'untuk_mahasiswa_eksekutif' => $executive ? 1 : 0,
            'cakupan_ujian' => $scope,
            'jenis_tugas_akhir_ids' => $finalProjectTypeIds,
        ];
    }
}
