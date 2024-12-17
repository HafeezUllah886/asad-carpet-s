<?php

use App\Http\Controllers\SalePaymentsController;
use App\Http\Controllers\SalesController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('sale', SalesController::class);

    Route::get("sales/getproduct/{id}", [SalesController::class, 'getSignleProduct']);
    Route::get("sales/delete/{id}", [SalesController::class, 'destroy'])->name('sale.delete')->middleware(confirmPassword::class);



});
