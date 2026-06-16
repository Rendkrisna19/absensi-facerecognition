<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlpaService;
use Carbon\Carbon;

class CreateAlpaRecords extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'absensi:create-alpa
                            {--date= : Specific date (Y-m-d). Defaults to today.}
                            {--backfill : Backfill all past working days from start of current month to yesterday.}
                            {--start= : Start date for backfill (Y-m-d). Used with --backfill.}
                            {--end= : End date for backfill (Y-m-d). Used with --backfill. Defaults to yesterday.}';

    /**
     * The console command description.
     */
    protected $description = 'Create Alpa (absent) records for gurus who did not attend on a given working day.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('backfill')) {
            $startDate = $this->option('start') ?? Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $this->option('end') ?? Carbon::now()->subDay()->format('Y-m-d');

            $this->info("Backfilling Alpa records from {$startDate} to {$endDate}...");

            $created = AlpaService::backfillAlpaRecords($startDate, $endDate);

            $this->info("Done. {$created} Alpa record(s) created.");
            return Command::SUCCESS;
        }

        $date = $this->option('date') ?? Carbon::now()->format('Y-m-d');

        $this->info("Creating Alpa records for date: {$date}...");

        if (!AlpaService::isWorkingDay($date)) {
            $this->warn("{$date} is not a working day. Skipping.");
            return Command::SUCCESS;
        }

        $created = AlpaService::createAlpaRecords($date);

        $this->info("Done. {$created} Alpa record(s) created for {$date}.");
        return Command::SUCCESS;
    }
}
