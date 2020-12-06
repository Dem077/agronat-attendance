<?php

namespace Database\Seeders;

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
            'username'=>'hassaan',
            'fullname'=>'Ibrahim Hassaan',
            'position'=>'Software',
            'password'=>Hash::make('secret'),
            'email'=>'hassaan@email.com'
            ]
        );

        $user->timesheet()->create(['punch'=>'2020-12-05 07:55']);
        $user->timesheet()->create(['punch'=>'2020-12-05 16:15']);
        $user->timesheet()->create(['punch'=>'2020-12-05 18:55']);
        $user->timesheet()->create(['punch'=>'2020-12-05 20:55']);
    }
}
