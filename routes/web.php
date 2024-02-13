<?php

use Illuminate\Support\Facades\Route;
use Netto\Http\Controllers\DeliveryController;
use Netto\Http\Controllers\GroupController;
use Netto\Http\Controllers\MerchandiseController;
use Netto\Http\Controllers\OrderController;
use Netto\Http\Controllers\OrderStatusController;
use Netto\Http\Controllers\PriceController;

Route::prefix(CMS_LOCATION)->name('admin.')->group(function() {
    Route::middleware(['admin', 'verified'])->group(function() {
        Route::resource('store/status', OrderStatusController::class)->except(['toggle', 'index']);
        Route::resource('store/price', PriceController::class)->except(['toggle']);
        Route::resource('store/delivery', DeliveryController::class);
        Route::resource('store/group', GroupController::class);
        Route::resource('store/merchandise', MerchandiseController::class)->except(['index']);
        Route::resource('store/order', OrderController::class)->except(['create', 'toggle']);
    });
});
