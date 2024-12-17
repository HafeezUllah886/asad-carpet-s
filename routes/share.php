<?php

use App\Http\Controllers\shareController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get("sales/share/{id}", [shareController::class, 'share']);
    Route::get("statement/share/{id}/{from}/{to}", [shareController::class, 'shareStatement']);

});

Route::get('/pdf/{file}', [shareController::class, 'getfile']);
