<?php

use App\Http\Controllers\EasyPageController;
use Illuminate\Support\Facades\Route;

Route::get('/uhod_instruction', [EasyPageController::class, 'uhod_instruction'])->name('uhod_instruction');
Route::get('/policy', [EasyPageController::class, 'show_policy'])->name('policy');
Route::get('/policy_accept', [EasyPageController::class, 'policy_accept'])->name('policy_accept');
Route::get('/cookie', [EasyPageController::class, 'show_cookie_policy'])->name('cookie');
Route::get('/review_consent', [EasyPageController::class, 'show_review_consent'])->name('review_consent');

Route::get('/bonus_system', [EasyPageController::class, 'show_bonus_system'])->name('bonus_system');
Route::get('/about', [EasyPageController::class, 'show_about'])->name('about');

Route::get('/warranty', [EasyPageController::class, 'show_warranty'])->name('warranty');