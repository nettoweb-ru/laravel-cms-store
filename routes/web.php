<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{
    SectionController,
    MerchandiseController,
    OrderController
};
use Netto\Http\Controllers\Admin\{
    DeliveryController,
    OrderStatusController,
    PriceController
};

Route::prefix(config('cms.location'))->name('admin.')->group(function() {
    Route::middleware(['admin', 'verified'])->prefix('store')->name('store.')->group(function() {
        Route::middleware('permission:admin-store-prices')->group(function() {
            Route::resource('price', PriceController::class)->except(['toggle']);
        });

        Route::middleware('permission:admin-store-deliveries')->group(function() {
            Route::resource('delivery', DeliveryController::class);
        });

        Route::middleware('permission:admin-store-merchandise')->group(function() {
            Route::resource('section', SectionController::class)->except(['index']);
            Route::resource('merchandise', MerchandiseController::class);
        });

        Route::middleware('permission:admin-store-orders')->group(function() {
            Route::resource('order', OrderController::class)->except(['toggle']);
            Route::resource('status', OrderStatusController::class)->except(['toggle', 'index']);
        });
    });
});
