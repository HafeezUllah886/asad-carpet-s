<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\WarehousesController;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/purchase.php';
require __DIR__ . '/stock.php';
require __DIR__ . '/sale.php';
require __DIR__ . '/stockTransfer.php';
require __DIR__ . '/reports.php';
require __DIR__ . '/share.php';

Route::middleware('auth')->group(function () {

    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');

    Route::resource('warehouses', WarehousesController::class);

    Route::resource('product', ProductsController::class);


});


