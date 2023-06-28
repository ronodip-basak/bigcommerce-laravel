<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/list', function () {
    return Inertia::render('List');
});

Route::group(['prefix' => 'auth'], function () {
    Route::get('install', [IndexController::class, 'install']);

    Route::get('load', [IndexController::class, 'load']);

    Route::get('uninstall', function () {
        echo 'uninstall';
        return app()->version();
    });

    Route::get('remove-user', function () {
        echo 'remove-user';
        return app()->version();
    });
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::any('/bc-api/{endpoint?}', [IndexController::class, 'proxyToBigCommerce'])->where('endpoint', '(.*)');


require __DIR__.'/auth.php';
