<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Webkul\Papara\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web']], function () {

    /**
     * Papara payment routes
     */
    Route::get('/papara-redirect', [PaymentController::class, 'redirect'])->name('papara.redirect');

    Route::get('/papara-success', [PaymentController::class, 'success'])->name('papara.success');

    Route::get('/papara-cancel', [PaymentController::class, 'failure'])->name('papara.cancel');

    Route::post('/papara-callback', [PaymentController::class, 'callback'])->name('papara.callback')
        ->withoutMiddleware(VerifyCsrfToken::class);
});
