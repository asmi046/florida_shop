<?php

declare(strict_types=1);

use Tabuna\Breadcrumbs\Trail;
use Illuminate\Support\Facades\Route;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Blog\BlogEditScreen;
use App\Orchid\Screens\Blog\BlogListScreen;
use App\Orchid\Screens\Options\EditOptions;
use App\Orchid\Screens\Options\OptionsList;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\Blog\BlogCreateScreen;
use App\Orchid\Screens\Revew\RevewListScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\User\UserProfileScreen;

use App\Orchid\Screens\Product\ProductEditScreen;
use App\Orchid\Screens\Product\ProductListScreen;
use App\Orchid\Screens\Category\CategoryEditScreen;

use App\Orchid\Screens\Category\CategoryListScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Product\ProductCreateScreen;

use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Category\CategoryCreateScreen;

use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\ProductTag\ProductTagEditScreen;
use App\Orchid\Screens\ProductTag\ProductTagListScreen;


use App\Orchid\Screens\Celebration\CelebrationEditScreen;
use App\Orchid\Screens\Celebration\CelebrationListScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\Celebration\CelebrationCreateScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Отзывы
Route::screen('/revews', RevewListScreen::class)
    ->name('platform.revews')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Отзывы')));

// Категории
Route::screen('/categories', CategoryListScreen::class)
    ->name('platform.category')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Категории товаров'), route('platform.category')));

Route::screen('/categories/{id}/edit', CategoryEditScreen::class)
    ->name('platform.category_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.category')
    ->push(__('Редактирование категории'), route('platform.category_edit', $id)));

Route::screen('/categories/create', CategoryCreateScreen::class)
    ->name('platform.category_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.category')
    ->push(__('Добавление категории'), route('platform.category_create')));



// Праздники
Route::screen('/celebration', CelebrationListScreen::class)
    ->name('platform.celebration')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Праздники'), route('platform.celebration')));

Route::screen('/celebration/{id}/edit', CelebrationEditScreen::class)
    ->name('platform.celebration_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.celebration')
    ->push(__('Редактирование праздника'), route('platform.celebration_edit', $id)));

Route::screen('/celebration/create', CelebrationCreateScreen::class)
    ->name('platform.celebration_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.celebration')
    ->push(__('Добавление праздника'), route('platform.celebration_create')));

//Блог

Route::screen('/blog', BlogListScreen::class)
    ->name('platform.blog')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Блог'), route('platform.blog')));

Route::screen('/blog/{id}/edit', BlogEditScreen::class)
    ->name('platform.blog_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.blog')
    ->push(__('Редактирование статьи'), route('platform.blog_edit', $id)));

Route::screen('/blog/create', BlogCreateScreen::class)
    ->name('platform.blog_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.blog')
    ->push(__('Добавление статьи'), route('platform.blog_create')));


// Товары
Route::screen('/products', ProductListScreen::class)
    ->name('platform.product')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.index')
    ->push(__('Товары'), route('platform.product')));

Route::screen('/products/{id}/edit', ProductEditScreen::class)
    ->name('platform.product_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.product')
    ->push(__('Редактирование товара'), route('platform.product_edit', $id)));

Route::screen('/products/create', ProductCreateScreen::class)
    ->name('platform.product_create')->breadcrumbs(fn (Trail $trail) => $trail
    ->parent('platform.product')
    ->push(__('Добавление товара'), route('platform.product_create')));

// Опции
Route::screen('/options', OptionsList::class)
    ->name('platform.options')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Опции'), route("platform.options")));

Route::screen('/options/{id}/edit', EditOptions::class)
    ->name('platform.options_edit')->breadcrumbs(fn (Trail $trail, $id) => $trail
    ->parent('platform.options')
    ->push(__('Редактирование опции'), route('platform.options_edit', $id)));






// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push(__('User'), route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Role'), route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example screen'));

// Группировка роутов для тегов продуктов
Route::prefix('product-tags')->name('platform.product-tags.')->group(function () {

    // Список тегов
    Route::screen('/', ProductTagListScreen::class)
        ->name('list')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push('Теги продуктов');
        });

    // Создание нового тега
    Route::screen('create', ProductTagEditScreen::class)
        ->name('create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.product-tags.list')
                ->push('Создать тег');
        });

    // Редактирование существующего тега
    Route::screen('{tag}/edit', ProductTagEditScreen::class)
        ->name('edit')
        ->breadcrumbs(function (Trail $trail, $tag) {
            return $trail
                ->parent('platform.product-tags.list')
                ->push('Редактировать: ' . $tag->title);
        });
});

Route::screen('example-fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('example-layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('example-charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('example-editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('example-cards', ExampleCardsScreen::class)->name('platform.example.cards');
Route::screen('example-advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');


//Route::screen('idea', Idea::class, 'platform.screens.idea');
