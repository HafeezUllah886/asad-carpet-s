<?php

use App\Http\Controllers\SalePaymentsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StockTransferController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('stockTransfer', StockTransferController::class);

    Route::get("stockTransfer/delete/{id}", [StockTransferController::class, 'destroy'])->name('stockTransfer.delete')->middleware(confirmPassword::class);
});
