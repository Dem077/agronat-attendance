<?php

use App\Http\Livewire\AssignRole;
use App\Http\Livewire\AttendanceComponent;
use App\Http\Livewire\AttendanceReport;
use App\Http\Livewire\DashboardComponent;
use App\Http\Livewire\OvertimeComponent;
use App\Http\Livewire\ResetPassword;
use App\Http\Livewire\RolesComponent;
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

// Route::get('/', function () {
//     return view('welcome');
// })->name('employee.dashboard');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('employees',UsersComponent::class)->name('employees');
    Route::get('attendances',AttendanceComponent::class)->name('attendances');
    Route::get('overtime',OvertimeComponent::class)->name('overtime');
    Route::get('timesheets',TimeSheetComponent::class)->name('timesheets');
    Route::get('/', DashboardComponent::class)->name('dashboard');
    Route::get('/reset-password',ResetPassword::class)->name('reset-password');
    Route::get('roles',RolesComponent::class)->name('roles');
    Route::get('assign-roles',AssignRole::class)->name('assign-roles');
    Route::get('attendance-report',AttendanceReport::class)->name('reports.attendance');
});

