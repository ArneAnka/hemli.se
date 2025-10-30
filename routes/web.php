<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\TryUploadController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

# Guest upload
Route::middleware('throttle:guest-try-upload')
    ->post('/try/upload', [TryUploadController::class, 'store'])->name('try.upload');

// Strömmar rå KRYPTTEXT (bytes) för XHR/hämtning i vy-sidan
Route::get('/try/blob/{token}', [TryUploadController::class, 'blob'])->name('try.blob');

// Vy-sida som dekrypterar i webbläsaren (Inertia)
Route::get('/try/v/{token}', [TryUploadController::class, 'view'])->name('try.view');

require __DIR__.'/settings.php';
