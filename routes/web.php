<?php
/**
 * Web route'lari (Laravel uslubidagi Route::get/post).
 *
 * Bu namuna Laravel'ning yangi install'idagi kabi toza:
 *   /            -> welcome sahifasi  (Laravel: welcome.blade.php)
 *   /dashboard   -> auth bilan himoyalangan sahifa
 *   /docs, /docs/{name} -> hujjatlar viewer (markdown render)
 *   /register, /login, /logout -> auth
 */

use App\Controllers\WelcomeController;
use App\Controllers\AuthController;
use App\Controllers\DocsController;

// Bosh sahifa — Laravel'ning welcome sahifasiga to'g'ri keladi.
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Auth bilan himoyalangan dashboard (Laravel breeze'dagi /dashboard kabi).
Route::get('/dashboard', [WelcomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// Hujjatlar — docs/ papkadagi markdown fayllarni render qiluvchi viewer.
Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');
// install.php yuklab olish (docs/{name} dan oldin — aniqli route).
Route::get('/docs/installatsiya/yuklab', [DocsController::class, 'installDownload'])->name('docs.install');
Route::get('/docs/{name}', [DocsController::class, 'show'])->name('docs.show');

// Auth
Route::get('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'store'])->middleware('guest');
Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');