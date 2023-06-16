<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EasyPageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TovarController;

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SenderController;

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\SearchController;

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\PayController;

use App\Http\Controllers\TestController;

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

Route::get('/test_api', [TestController::class, "index"])->name('test_api');

Route::get('/', [IndexController::class, "show"])->name('home');
Route::get('/catalog', [CategoryController::class, "show"])->name('catalog');
Route::get('/catalog/{slug}', [CategoryController::class, "show_cat"])->name('category');
Route::get('/tovar/{slug}', [TovarController::class, "show"])->name('tovar');

Route::get('/contacts', [ContactsController::class, "show"])->name('contacts');
Route::get('/zones', [ContactsController::class, "show_zones"])->name('zones');
Route::get('/blog', [BlogController::class, "show"])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, "show_page"])->name('blog_page');
Route::get('/reviews', [ReviewsController::class, "show"])->name('reviews');

Route::get('/policy', [EasyPageController::class, "show_policy"])->name('policy');
Route::get('/bonus_system', [EasyPageController::class, "show_bonus_system"])->name('bonus_system');
Route::get('/about', [EasyPageController::class, "show_about"])->name('about');

Route::get('/thencs', [SenderController::class, "show_thencs"])->name('thencs');
Route::post('/send_consult', [SenderController::class, "send_consultation"])->name('send_consultation');

Route::get('/bascet/thencs', [CartController::class, "thencs"])->name("bascet_thencs");
Route::get('/bascet', [CartController::class, "index"])->name("bascet");
Route::post('/bascet/add', [CartController::class, "add"])->name("bascet_add");
Route::post('/bascet/update', [CartController::class, "update"])->name("bascet_update");
Route::get('/bascet/get', [CartController::class, "get_all"])->name("bascet_get");
Route::delete('/bascet/clear', [CartController::class, "clear"])->name("bascet_clear");
Route::delete('/bascet/delete', [CartController::class, "delete"])->name("bascet_delete");
Route::post('/bascet/send', [CartController::class, "send"])->name("bascet_send");


Route::get('/favorites', [FavoriteController::class, "index"])->name("favorites");
Route::get('/favorites/get', [FavoriteController::class, "get_all"])->name("favorites_get");
Route::post('/favorites/add', [FavoriteController::class, "add"])->name("favorites_add");
Route::delete('/favorites/delete', [FavoriteController::class, "delete"])->name("favorites_delete");
Route::delete('/favorites/clear', [FavoriteController::class, "clear"])->name("favorites_clear");

Route::get('/search_pds', [SearchController::class, 'search_pds'])->name('search_pds');
Route::get('/search', [SearchController::class, 'show_search_page'])->name('show_search_page');

Route::get('/payinfo', [PayController::class, 'show_payinfo'])->name('show_payinfo');

Route::middleware('auth')->group(function () {
    Route::get('/cabinet', [CabinetController::class, "show_cabinet_main"])->name("cabinet.home");
    Route::get('/cabinet/orders', [CabinetController::class, "show_cabinet_orders"])->name("cabinet.orders");

    Route::get('/logout', [AuthController::class, "logout"])->name("logout");
    Route::post('/save_user', [AuthController::class, "save_user_data"])->name("save_user_data");
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, "show_login_form"])->name("login");
    Route::post('/login_do', [AuthController::class, "login"])->name("login_do");

    Route::get('/passrec', [AuthController::class, "show_passrec_form"])->name("passrec");
    Route::post('/pass_rec_do', [AuthController::class, "pass_req"])->name("pass_rec_do");

    Route::post('/register_do', [AuthController::class, "register"])->name("register_do");
    Route::get('/register', [AuthController::class, "show_register_form"])->name("register");
});
