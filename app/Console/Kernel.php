<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('check-news')
                ->everyMinute();
        $schedule->exec('rm public/images/avatars/*.tmp')
                ->daily();
        $schedule->command('check-sales')
                ->everyMinute();
        $schedule->command('reset-foraging')
                ->daily();
        $schedule->command('update-timed-daily')
                ->everyMinute();          
        $schedule->command('check-pet-drops')
                ->everyMinute();
        $schedule->command('restock-shops')
                ->daily();
        $schedule->command('update-timed-stock')
                ->everyMinute();
        $schedule->command('distribute-birthday-rewards')
                ->monthly();
        $schedule->command('change-feature')
                ->monthly();
        $schedule->command('change-fetch-item')
                ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
