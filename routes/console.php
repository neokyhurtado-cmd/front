<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler para automatización del blog
Schedule::command('feeds:fetch')->hourly()->withoutOverlapping();
// Mobility feed refresh
Schedule::command('mobility:refresh')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('posts:schedule-daily')->dailyAt('07:55');   // prepara slots del día
Schedule::command('posts:publish-due')->everyMinute();         // publica en la hora exacta
Schedule::command('posts:rotate-monthly')->monthlyOn(1, '03:10');

// Sitemap semanal
Schedule::call(function () {
    \Spatie\Sitemap\SitemapGenerator::create(config('app.url'))
        ->writeToFile(public_path('sitemap.xml'));
})->weekly()->sundays()->at('02:30');
