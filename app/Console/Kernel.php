<?php

protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
{
    $schedule->command('guest:clean')->dailyAt('03:00');
}