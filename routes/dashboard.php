<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Маршруты дашборда
|--------------------------------------------------------------------------
|
| Защищены токеном (?token=...) через middleware dashboard.token.
|
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// Детализация состава букета по техкарте МойСклад
Route::get('/product/{product}', [DashboardController::class, 'product'])->name('dashboard.product');
