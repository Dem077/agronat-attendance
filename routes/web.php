<?php

use App\Http\Livewire\AttendanceComponent;
use App\Http\Livewire\TimeSheetComponent;
use App\Http\Livewire\Users as UsersComponent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('employee.dashboard');

Route::get('employees',UsersComponent::class)->name('employees');
Route::get('attendances',AttendanceComponent::class)->name('attendances');
Route::get('timesheets',TimeSheetComponent::class)->name('timesheets');