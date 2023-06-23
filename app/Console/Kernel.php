<?php

namespace App\Console;

//use App\Http\Controllers\Entities\EntitiesUpdateController;
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
         $schedule->call('App\Http\Controllers\Entities\EntitiesUpdateController@checkBDforErrors')
             ->name('transfer')
//             ->everyFifteenMinutes();
             ->everyTwoMinutes();

        $schedule->call('App\Http\Controllers\VerifiedUserController@store')
            ->name('transfer.users')
            ->everyThreeHours();
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
