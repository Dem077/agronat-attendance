<?php

use App\Http\Livewire\ApplyOtComponent;
use App\Http\Livewire\AssignRole;
use App\Http\Livewire\AttendanceComponent;
use App\Http\Livewire\AttendanceReport;
use App\Http\Livewire\DashboardComponent;
use App\Http\Livewire\DepartmentComponent;
use App\Http\Livewire\Holidays\ListHolidays;
use App\Http\Livewire\LogRecompute;
use App\Http\Livewire\OTReport;
use App\Http\Livewire\OvertimeComponent;
use App\Http\Livewire\ResetPassword;
use App\Http\Livewire\RolesComponent;
use App\Http\Livewire\TimeSheetComponent;
use App\Http\Livewire\LeavesComponent;
use App\Http\Livewire\LeaveTypeComponent;
use App\Http\Livewire\Overtime\PreOtRequest as OvertimePreOtRequest;
use App\Http\Livewire\Overtime\PreOtRequest\Add as OvertimePreOtRequestAdd;
use App\Http\Livewire\Reports\LeaveReport;
use App\Http\Livewire\Timesheets\ImportLog;
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
    Route::get('/', DashboardComponent::class)->name('dashboard');
    Route::get('employees',UsersComponent::class)->name('employees');
    Route::get('attendances',AttendanceComponent::class)->name('attendances');
    Route::get('overtime',OvertimeComponent::class)->name('overtime');
    Route::get('overtime/pre-ot-request',OvertimePreOtRequest::class)->name('overtime.pre-ot-request');
    Route::get('overtime/pre-ot-request/create',OvertimePreOtRequestAdd::class)->name('overtime.pre-ot-request.create');

    Route::get('applied-overtime',ApplyOtComponent::class)->name('applied-overtime');

    Route::get('timesheets1',TimeSheetComponent::class)->name('timesheets');
    Route::get('timesheet/import-log',ImportLog::class)->name('timesheet.import-log');


    Route::get('/reset-password',ResetPassword::class)->name('reset-password');
    Route::get('roles',RolesComponent::class)->name('roles');
    Route::get('assign-roles',AssignRole::class)->name('assign-roles');
    Route::get('attendance-report',AttendanceReport::class)->name('reports.attendance');
    Route::get('leave-report',LeaveReport::class)->name('reports.leave');
    Route::get('ot-report',OTReport::class)->name('reports.ot');
    Route::get('leaves',LeavesComponent::class)->name('leaves');
    Route::get('leaves-types',LeaveTypeComponent::class)->name('leave-types');
    Route::get('departments',DepartmentComponent::class)->name('departments');
    Route::get('holidays',ListHolidays::class)->name('holidays.list');

    Route::get('recompute',LogRecompute::class)->name('recompute');
});
