<?php

use Illuminate\Support\Facades\Route;
use Chargily\ChargilyProLaravel\Http\Controllers\Api\V1\WebhookController;

Route::group(['as' => "chargily-pro.api.", 'middleware' => ['api'], 'prefix' => "chargily-pro/api/v1/"], function () {
    // =========================
    // Handle webhook request ==
    // =========================
    Route::post('/topup-webhook-handle', [WebhookController::class, 'handle'])->name("topup-webhook");
});
