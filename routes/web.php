<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

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

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'customLogin'])->name('login');
Route::middleware(['auth', 'adminCheck'])->group(function () {
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
    Route::get('/users', [UserController::class, 'users'])->name('users');
    Route::get('/view/user/{id}/activities', [UserController::class, 'viewUser'])->name('viewUser');
    Route::post('/activity', [AdminController::class, 'addActivity'])->name('activity');
    Route::get('/fetch-activities', [AdminController::class, 'fetchActivities'])->name('fetchActivities');
    Route::post('/edit/activity/date', [AdminController::class, 'editActivityDate'])->name('editActivityDate');
    Route::delete('/delete/activity', [AdminController::class, 'deleteActivity'])->name('deleteActivity');
    Route::delete('/delete/user/activity', [AdminController::class, 'deleteUserActivity'])->name('deleteUserActivity');
    Route::post('/edit/activity', [AdminController::class, 'editActivity'])->name('editActivity');
    Route::post('/edit/user/activity', [AdminController::class, 'editUserActivity'])->name('editUserActivity');
    Route::post('/add/user/activity', [AdminController::class, 'addUserActivity'])->name('addUserActivity');
});