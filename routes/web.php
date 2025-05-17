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

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::post('checkin', [AttendanceController::class, 'storeCheckIn'])->name('checkin.store');
        Route::post('checkout', [AttendanceController::class, 'storeCheckOut'])->name('checkout.store');

        Route::get('history', [AttendanceController::class, 'history'])->name('history');
        Route::get('history/list', [AttendanceController::class, 'history.list'])->name('history.list');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/store', [SettingController::class, 'store'])->name('store');
    });

    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::post('/', [PositionController::class, 'store'])->name('store');
        Route::get('/{position}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::put('/{position}', [PositionController::class, 'update'])->name('update');
        Route::delete('/{position}', [PositionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/list', [UserController::class, 'list'])->name('list');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('excuses')->name('excuses.')->group(function () {
        Route::get('/', [ExcuseController::class, 'index'])->name('index');
        Route::post('/', [ExcuseController::class, 'store'])->name('store');
        Route::put('/{id}', [ExcuseController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExcuseController::class, 'destroy'])->name('destroy');

        Route::get('history', [ExcuseController::class, 'history'])->name('history');
        Route::get('history/list', [ExcuseController::class, 'historyList'])->name('history.list');

        Route::patch('/{excuse}/update-status', [ExcuseController::class, 'updateStatus'])->name('updateStatus');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AuthController::class, 'showProfile'])->name('show');
        Route::put('/', [AuthController::class, 'updateProfile'])->name('update');
    });

});
