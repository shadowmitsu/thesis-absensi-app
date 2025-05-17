<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcuseController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('attendance/checkin', [AttendanceController::class, 'storeCheckIn'])->name('attendance.checkin.store');
    Route::post('attendance/checkout', [AttendanceController::class, 'storeCheckOut'])->name('attendance.checkout.store');

    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/history/list', [AttendanceController::class, 'historyList'])->name('attendance.history.list');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/store', [SettingController::class, 'store'])->name('settings.store');


    Route::get('positions', [PositionController::class, 'index'])->name('positions.index');
    Route::post('positions', [PositionController::class, 'store'])->name('positions.store');
    Route::get('positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
    Route::put('positions/{position}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');


    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/excuses', [ExcuseController::class, 'index'])->name('excuses.index');
    Route::post('/excuses', [ExcuseController::class, 'store'])->name('excuses.store');
    Route::put('/excuses/{id}', [ExcuseController::class, 'update'])->name('excuses.update');
    Route::delete('/excuses/{id}', [ExcuseController::class, 'destroy'])->name('excuses.destroy');

    Route::get('/excuses/history', [ExcuseController::class, 'history'])->name('excuses.history');
    Route::get('/excuses/history/list', [ExcuseController::class, 'historyList'])->name('excuses.history.list');
    Route::patch('/excuses/{excuse}/update-status', [ExcuseController::class, 'updateStatus'])->name('excuses.updateStatus');

});