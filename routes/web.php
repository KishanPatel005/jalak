<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReturnController;

use App\Http\Controllers\LoginController;

// Auth Routes
Route::get('/init-user', function() {
    \App\Models\User::updateOrCreate(
        ['email' => 'kishan7112@gmail.com'],
        ['name' => 'Kishan', 'password' => Hash::make('123')]
    );
    return "User kishan7112@gmail.com initialized with pass 123. Please delete this route after use.";
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
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
    Route::post('/manage-product/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('/manage-product/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/manage-product/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/manage-product/delete/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Rent Routes
    Route::get('/rent', [BookingController::class, 'index'])->name('rent.index');
    Route::get('/manage-booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/manage-booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/manage-booking/edit/{id}', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::post('/manage-booking/update/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/manage-booking/delete/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Invoice Center
    Route::get('/invoices', [BookingController::class, 'invoices'])->name('invoices.index');

    // Delivery Routes
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/manage-delivery/{id}', [DeliveryController::class, 'manage'])->name('deliveries.manage');
    Route::post('/manage-delivery/update/{id}', [DeliveryController::class, 'update'])->name('deliveries.update');

    // Return Routes
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/manage-return/{id}', [ReturnController::class, 'manage'])->name('returns.manage');
    Route::post('/manage-return/update/{id}', [ReturnController::class, 'update'])->name('returns.update');

    // API Search Routes
    Route::get('/api/search-customers', [BookingController::class, 'searchCustomer']);
    Route::get('/api/search-products', [BookingController::class, 'searchProduct']);
    Route::get('/api/check-availability', [BookingController::class, 'checkAvailability']);
    Route::get('/api/product-stock', [BookingController::class, 'getProductStock']);

    // Invoice PDF
    Route::get('/booking/invoice/{id}', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');

    Route::post('/manage-return/update-item/{id}', [ReturnController::class, 'updateItemReturn'])->name('returns.updateItem');
    Route::post('/manage-return/finish/{id}', [ReturnController::class, 'finish'])->name('returns.finish');
});