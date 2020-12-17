<?php

namespace Database\Seeders;

use App\Jobs\AddSchedule;
use App\Jobs\UpdateAttendanceStatus;
use App\Models\Holiday;
use App\Models\TimeSheet;
use App\Models\User;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user=User::create([
            'name'=>'Ibrahim Hassaan',
            'designation'=>'Software',
            'password'=>Hash::make('secret'),
            'email'=>'hassaan@email.com',
            'external_id'=>4
            ]);
        

        $begin=new DateTime('first day of this month');
        $end=new DateTime('last day of this month');

        $this->addHolidays($begin,$end);

        $begin=new DateTime('first day of this month');
        AddSchedule::dispatchNow(['from'=>$begin->format('Y-m-d'),'to'=>$end->format('Y-m-d')]);

        //create schedule
        
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 07:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 11:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 12:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 16:15']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 18:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 20:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 07:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 11:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 12:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 16:15']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 18:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-06 20:55']);


        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-08 7:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-08 16:55']);

        UpdateAttendanceStatus::dispatchNow(['from'=>$begin->format('Y-m-d'),'to'=>$end->format('Y-m-d')]);
    }

    private function addHolidays($begin,$end){
        while(True){
            $begin->modify('Next Friday');
            if($begin>$end){
                break;
            }
            Holiday::create(['h_date'=>$begin->format('Y-m-d')]);
            $begin->modify('Next Saturday');
            if($begin>$end){
                break;
            }
            Holiday::create(['h_date'=>$begin->format('Y-m-d')]);
        }
    }
}
