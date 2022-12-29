<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EasyPageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TovarController;
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

Route::get('/', [IndexController::class, "show"])->name('home');
Route::get('/catalog', [CategoryController::class, "show"])->name('category');
Route::get('/tovar', [TovarController::class, "show"])->name('tovar');
Route::get('/zone', [EasyPageController::class, "zone"])->name('zone');
