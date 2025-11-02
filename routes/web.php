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
            Route::get('price/csv', [PriceController::class, 'csv'])->name('price.download-csv');
        });

        Route::middleware('permission:admin-store-deliveries')->group(function() {
            Route::resource('delivery', DeliveryController::class);
            Route::get('delivery/csv', [DeliveryController::class, 'csv'])->name('delivery.download-csv');
        });

        Route::middleware('permission:admin-store-merchandise')->group(function() {
            Route::resource('section', SectionController::class)->except(['index']);
            Route::get('section/csv', [SectionController::class, 'csv'])->name('section.download-csv');

            Route::resource('merchandise', MerchandiseController::class);
            Route::get('merchandise/csv', [MerchandiseController::class, 'csv'])->name('merchandise.download-csv');
        });

        Route::middleware('permission:admin-store-orders')->group(function() {
            Route::resource('order', OrderController::class)->except(['toggle']);
            Route::get('order/csv', [OrderController::class, 'csv'])->name('order.download-csv');

            Route::resource('status', OrderStatusController::class)->except(['toggle', 'index']);
            Route::get('status/csv', [OrderStatusController::class, 'csv'])->name('status.download-csv');
        });
    });
});
