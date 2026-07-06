<?php

use App\Http\Controllers\Api\RatingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['hmac.verify', 'throttle:survey-ratings'])
    ->group(function () {
        Route::post('/ratings', [RatingController::class, 'store']);
    });
