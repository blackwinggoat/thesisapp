<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LinkBimbinganTopik extends Command
{
    protected $signature = 'thesis:link-bimbingan-topik {--apply : Link only unambiguous accepted topics to guidance records}';

    protected $description = 'Link guidance records to their single accepted student topic without overwriting titles';

    public function handle()
    {
        if (!Schema::hasColumn('trt_bimbingan', 'topik_id')) {
            throw new RuntimeException('The trt_bimbingan.topik_id column is required. Run migrations first.');
        }

        $candidates = $this->candidates();
        $unlinked = DB::table('trt_bimbingan')->whereNull('topik_id')->count();
        $alreadyLinked = DB::table('trt_bimbingan')->whereNotNull('topik_id')->count();
        $ambiguous = $unlinked - $candidates->count();

        $this->line('Already linked guidance rows: ' . $alreadyLinked);
        $this->line('Unambiguous links available: ' . $candidates->count());
        $this->line('Unlinked rows skipped (no or multiple accepted topics): ' . $ambiguous);
        $this->line('Titles and types are not changed by this command.');

        if (!$this->option('apply')) {
            $this->warn('Dry run only. Re-run with --apply after a verified database backup.');

            return 0;
        }

        DB::transaction(function () use ($candidates) {
            foreach ($candidates as $candidate) {
                DB::table('trt_bimbingan')
                    ->where('bimbingan_id', $candidate->bimbingan_id)
                    ->whereNull('topik_id')
                    ->update(['topik_id' => $candidate->topik_id]);
            }
        });

        $this->info('Guidance-topic links created: ' . $candidates->count());

        return 0;
    }

    private function candidates()
    {
        return DB::table('trt_bimbingan as b')
            ->join('trt_topik as t', 't.C_NPM', '=', 'b.C_NPM')
            ->whereNull('b.topik_id')
            ->where('t.status', 1)
            ->groupBy('b.bimbingan_id')
            ->havingRaw('COUNT(t.topik_id) = 1')
            ->selectRaw('b.bimbingan_id, MIN(t.topik_id) AS topik_id')
            ->get();
    }
}
