<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReturnController;

Route::get('/', function () {
    // Inventory
    $totalProducts = \App\Models\Product::count();
    $totalQty = \App\Models\ProductStock::sum('qty');

    // Deliveries
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    $todayDeliveries = \App\Models\BookingItem::where('from_date', $today)->count();
    
    $tomorrowDeliveries = \App\Models\BookingItem::where('from_date', $tomorrow)->count();
    $tomorrowPacked = \App\Models\BookingItem::where('from_date', $tomorrow)->where('is_packed', true)->count();
    $tomorrowUnpacked = \App\Models\BookingItem::where('from_date', $tomorrow)->where('is_packed', false)->count();

    // Returns
    $todayReturns = \App\Models\BookingItem::where('to_date', $today)->count();
    $tomorrowReturns = \App\Models\BookingItem::where('to_date', $tomorrow)->count();

    // General Stats
    $totalBooking = \App\Models\Booking::count();
    $totalDelivered = \App\Models\Booking::whereIn('status', ['dispatched', 'finished'])->count();

    // Recent Transactions
    $recentTransactions = \App\Models\Booking::with('customer')->latest()->take(6)->get();

    return view('index', compact(
        'totalProducts', 'totalQty', 
        'todayDeliveries', 'tomorrowDeliveries', 'tomorrowPacked', 'tomorrowUnpacked',
        'todayReturns', 'tomorrowReturns',
        'totalBooking', 'totalDelivered',
        'recentTransactions'
    ));
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

// Invoice Center
Route::get('/invoices', [BookingController::class, 'invoices'])->name('invoices.index');

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