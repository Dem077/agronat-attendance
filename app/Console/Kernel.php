<?php

namespace App\Console;

use App\Jobs\AddSchedule;
use App\Jobs\UpdateAttendanceStatus;
use App\Jobs\ZKTSync;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

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
        // $schedule->command('inspire')->hourly();
        $schedule->job(new ZKTSync())
                        ->everyThreeMinutes();
        $schedule->job(new AddSchedule([]))
                        ->everySixHours()
                        ->between('05:00','13:00');;
        $schedule->job(new UpdateAttendanceStatus([]))
                        ->everyThreeHours()
                        ->between('08:00','12:00');
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
