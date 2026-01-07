<?php

use Illuminate\Support\Facades\Route;
use Chargily\ChargilyProLaravel\Http\Controllers\Web\MainController;

Route::group(['as' => "chargily-pro.", 'middleware' => ['web'], 'prefix' => "chargily-pro/"], function () {
    // ===============
    // Check status ==
    // ===============
    Route::any('/', [MainController::class, 'index'])->name("index");
});
