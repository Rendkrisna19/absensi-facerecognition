<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan pembuatan record Alpa setiap hari pukul 17:00 WIB
Schedule::command('absensi:create-alpa')->dailyAt('17:00');
