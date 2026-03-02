<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReturnController;

Route::get('/', function () {
    return view('index');
});

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/manage-product', [ProductController::class, 'create'])->name('products.create');
Route::get('/manage-product/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
Route::delete('/manage-product/delete/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('/manage-product', [ProductController::class, 'store'])->name('products.store');

// Rent/Booking Routes
Route::get('/rent', [BookingController::class, 'index'])->name('rent.index');
Route::get('/manage-booking', [BookingController::class, 'create'])->name('bookings.create');
Route::get('/manage-booking/edit/{id}', [BookingController::class, 'edit'])->name('bookings.edit');
Route::get('/manage-booking/invoice/{id}', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
Route::post('/manage-booking', [BookingController::class, 'store'])->name('bookings.store');
Route::post('/manage-booking/update/{id}', [BookingController::class, 'update'])->name('bookings.update');
Route::delete('/manage-booking/delete/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

// API Search Routes
Route::get('/api/search-customers', [BookingController::class, 'searchCustomer']);
Route::get('/api/search-products', [BookingController::class, 'searchProduct']);
Route::get('/api/check-availability', [BookingController::class, 'checkAvailability']);

// Delivery Routes
Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
Route::get('/manage-delivery/{id}', [DeliveryController::class, 'manage'])->name('deliveries.manage');
Route::post('/manage-delivery/update-packing/{id}', [DeliveryController::class, 'updatePacking'])->name('deliveries.updatePacking');
Route::post('/manage-delivery/dispatch/{id}', [DeliveryController::class, 'dispatch'])->name('deliveries.dispatch');

// Return Routes
Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
Route::get('/manage-return/{id}', [ReturnController::class, 'manage'])->name('returns.manage');
Route::post('/manage-return/update-item/{id}', [ReturnController::class, 'updateItemReturn'])->name('returns.updateItem');
Route::post('/manage-return/finish/{id}', [ReturnController::class, 'finish'])->name('returns.finish');