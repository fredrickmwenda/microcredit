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
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('reminders:send')->daily();
        $schedule->command('events:recur')->daily();
        //$schedule->command('penalties:process')->daily();
        $schedule->command('savings:interest')->daily();
        $schedule->command('loan:close-zero-balance')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        //process scheduled campai9gns 
        //campaigns:process
        $schedule->command('communication:process-scheduled-campaigns')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        //processing penalties on due loans       
        $schedule->command('loan:penalties-process')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        $schedule->command('savings:process_interest')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        $schedule->command('savings:calculate_interest')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
        
        //process reminders
        $schedule->command('reminders:process')
            ->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));
            
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
        $this->load(__DIR__ . '/Commands');
    }
}
