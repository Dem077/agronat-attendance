<?php

namespace Database\Seeders;

use App\Models\TimeSheet;
use App\Models\User;
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
            'fullname'=>'Ibrahim Hassaan',
            'position'=>'Software',
            'password'=>Hash::make('secret'),
            'email'=>'hassaan@email.com',
            'external_id'=>4
            ]
        );

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 07:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 11:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 12:55']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 16:15']);

        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 18:55']);
        TimeSheet::add(['user_id'=>$user->id,'punch'=>'2020-12-05 20:55']);
    }
}
