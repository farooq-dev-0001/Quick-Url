<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\AdminController;

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

// Home page - URL shortener
Route::get('/', [UrlController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// URL shortening routes
Route::post('/shorten', [UrlController::class, 'shorten'])->name('url.shorten');
Route::get('/stats/{shortCode}', [UrlController::class, 'stats'])->name('url.stats');

// API endpoint for creating short URLs (no api prefix)
Route::post('/create-short-link', [UrlController::class, 'apiShorten'])->name('url.api.shorten');

// Admin dashboard routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/urls', [AdminController::class, 'getUrls'])->name('urls');
    Route::get('/urls/datatable', [AdminController::class, 'getDatatableUrls'])->name('urls.datatable');
    Route::get('/urls/{id}/edit', [AdminController::class, 'editUrl'])->name('urls.edit');
    Route::delete('/urls/{id}', [AdminController::class, 'deleteUrl'])->name('urls.delete');
    Route::put('/urls/{id}', [AdminController::class, 'updateUrl'])->name('urls.update');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');
    Route::get('/top-urls', [AdminController::class, 'getTopUrls'])->name('top-urls');
    Route::get('/urls/export', [AdminController::class, 'exportUrls'])->name('urls.export');
});

// Short URL redirect (must be last to avoid conflicts)
Route::get('/{shortCode}', [UrlController::class, 'redirect'])->name('url.redirect');
