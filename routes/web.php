<?php

use App\Http\Livewire\AttendanceComponent;
use App\Http\Livewire\OvertimeComponent;
use App\Http\Livewire\TimeSheetComponent;
use App\Http\Livewire\Todos;
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
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('todos', Todos::class);
