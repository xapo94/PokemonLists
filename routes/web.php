<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Register
Route::get('/register', [RegisterController::class, 'create'])
    ->name('register')
    ->middleware('guest');

Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('guest');

// Log-In/Out
Route::get('/login', [SessionsController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [SessionsController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [SessionsController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// Profile
Route::get('/profile', [ProfileController::class, 'index'])
    ->name('profile')
    ->middleware('auth');

Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
    ->name('profile.password')
    ->middleware('auth');

Route::patch('/profile/pokemon', [ProfileController::class, 'updatePokemon'])
    ->name('profile.pokemon')
    ->middleware('auth');

// Teams
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index')->middleware('auth');
Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create')->middleware('auth');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store')->middleware('auth');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show')->middleware('auth');
Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit')->middleware('auth');
Route::patch('/teams/{team}', [TeamController::class, 'update'])->name('teams.update')->middleware('auth');
Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy')->middleware('auth');

require base_path('routes/api.php');
