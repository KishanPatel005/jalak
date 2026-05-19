@extends('layouts.admin')

@section('content')
<div class="mb-8" x-data="{ 
    isNewCustomer: false, 
    isBooked: {{ isset($booking) && $booking->status === 'confirmed' ? 'true' : 'false' }},
    customerId: '{{ $booking->customer_id ?? '' }}',
    customerName: '{{ $booking->customer->name ?? '' }}',
    customerMobile: '{{ $booking->customer->mobile ?? '' }}',
    customerAddress: '{{ $booking->customer->address ?? '' }}',
    customerQuery: '{{ isset($booking) ? $booking->customer->name . " (" . $booking->customer->mobile . ")" : "" }}',
    customerResults: [],
    products: {{ isset($booking) ? json_encode($booking->items->map(function($item) {
        return [
            'id' => $item->id,
            'query' => "{$item->product->name} [{$item->product->code}]",
            'results' => [],
            'selectedProductId' => $item->product_id,
            'selectedProductName' => $item->product->name,
            'selectedProductCode' => $item->product->code,
            'selectedSize' => $item->size,
            'fromDate' => $item->from_date,
            'toDate' => $item->to_date,
            'rentPrice' => $item->rent_price,
            'deposit' => $item->deposit_amount,
            'availableStocks' => $item->product->stocks,
            'checking' => false,
            'available' => true,
            'remaining' => 0,
            'history' => [],
            'days' => [],
            'showHistory' => false,
            'status' => $item->status
        ];
    })) : json_encode([[
        'id' => time(), 
        'query' => '', 
        'results' => [], 
        'selectedProductId' => '', 
        'selectedProductName' => '', 
        'selectedProductCode' => '', 
        'selectedSize' => '',
        'fromDate' => '', 
        'toDate' => '', 
        'rentPrice' => 0, 
        'deposit' => 0, 
        'availableStocks' => [],
        'checking' => false,
        'available' => true,
        'remaining' => 0,
        'history' => [],
        'days' => [],
        'showHistory' => false,
        'status' => 'pending'
    ]]) }},
    advanceInput: {{ isset($booking) ? ($booking->advance_paid ?? 0) : 'null' }},
    discount: {{ $booking->discount ?? 0 }},
    
    async checkAvailability(p) {
        if (!p.selectedProductId || !p.selectedSize) return;
        p.checking = true;
        try {
            let url = `/api/check-availability?product_id=${p.selectedProductId}&size=${p.selectedSize}`;
            if (p.fromDate) url += `&from_date=${p.fromDate}`;
            if (p.toDate) url += `&to_date=${p.toDate}`;
            
            const res = await fetch(url);
            const data = await res.json();
            p.available = data.available;
            p.remaining = data.remaining;
            p.history = data.history;
            p.days = data.days || [];
        } catch (e) {
            console.error('Availability check failed', e);
        } finally {
            p.checking = false;
        }
    },

    async searchCustomers() {
        if (this.customerQuery.length < 2) { this.customerResults = []; return; }
        const res = await fetch(`/api/search-customers?query=${this.customerQuery}`);
        this.customerResults = await res.json();
    },
    
    selectCustomer(c) {
        this.customerId = c.id;
        this.customerName = c.name;
        this.customerMobile = c.mobile;
        this.customerQuery = `${c.name} (${c.mobile})`;
        this.customerResults = [];
    },

    async searchProducts(p) {
        if (p.query.length < 2) { p.results = []; return; }
        const res = await fetch(`/api/search-products?query=${p.query}`);
        p.results = await res.json();
    },

    selectProduct(product, result) {
        product.selectedProductId = result.id;
        product.selectedProductName = result.name;
        product.selectedProductCode = result.code;
        product.query = `${result.name} [${result.code}]`;
        product.availableStocks = result.stocks;
        product.results = [];
    },

    updatePrices(product, size) {
        product.selectedSize = size;
        const stock = product.availableStocks.find(s => s.size === size);
        if (stock) {
            product.rentPrice = stock.rent_price;
            product.deposit = stock.deposit_amount;
        }
        this.checkAvailability(product);
    },

    calculateDays(from, to) {
        if (!from || !to) return 0;
        const start = new Date(from);
        const end = new Date(to);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        return diffDays > 0 ? diffDays : 0;
    },
    get totalRent() {
        return this.products.reduce((sum, p) => {
            const days = this.calculateDays(p.fromDate, p.toDate);
            return sum + (days * (parseFloat(p.rentPrice) || 0));
        }, 0);
    },
    get totalDeposit() {
        return this.products.reduce((sum, p) => sum + (parseFloat(p.deposit) || 0), 0);
    },
    get advance() {
        return this.advanceInput !== null && this.advanceInput !== '' ? (parseFloat(this.advanceInput) || 0) : (this.advanceInput === '' ? 0 : this.totalDeposit);
    },
    set advance(val) {
        this.advanceInput = val;
    },
    get grandTotal() {
        return (this.totalRent + this.totalDeposit) - (parseFloat(this.discount) || 0);
    },
    get remainingBalance() {
        return this.grandTotal - this.advance;
    },
    set remainingBalance(value) {
        let val = value === '' ? 0 : parseFloat(value);
        let diff = (this.totalRent + this.totalDeposit) - this.advance - val;
        this.discount = diff > 0 ? diff : 0;
    },
    get hasUnavailability() {
        return this.products.some(p => !p.available || p.checking);
    },
    addProduct() {
        this.products.push({ 
            id: Date.now(), 
            query: '', 
            results: [], 
            selectedProductId: '', 
            selectedProductName: '', 
            selectedProductCode: '', 
            selectedSize: '',
            fromDate: '', 
            toDate: '', 
            rentPrice: 0, 
            deposit: 0, 
            availableStocks: [],
            checking: false,
            available: true,
            remaining: 0,
            history: [],
            days: [],
            showHistory: false,
            status: 'pending'
        });
    }
}">
    <!-- Top Header & Breadcrumb -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2 text-sm text-slate-400">
            <a href="/rent" class="hover:text-blue-600">Rent</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600">Manage Booking</span>
        </div>
        <div class="flex items-center space-x-3">
             <span :class="isBooked ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'" class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest">
                <span x-text="isBooked ? 'Booked (Days Blocked)' : 'Draft (Days Not Blocked)'"></span>
            </span>
        </div>
    </div>
    <h2 class="text-3xl font-bold text-slate-800">Booking Management</h2>
    <p class="text-slate-500 mt-1 italic">Worker Section: Selection | Boss Section: Billing</p>

    <form action="{{ isset($booking) ? route('bookings.update', $booking->id) : route('bookings.store') }}" method="POST" class="space-y-8 mt-8">
        @csrf
        <input type="hidden" name="is_new_customer" :value="isNewCustomer">
        <input type="hidden" name="grand_total" :value="grandTotal">
        <input type="hidden" name="status" :value="isBooked ? 'confirmed' : 'draft'">

        <div class="space-y-8">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 relative">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30 rounded-t-2xl">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        1. Customer Selection
                    </h3>
                    <button type="button" @click="isNewCustomer = !isNewCustomer" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-lg transition-all">
                        <span x-show="!isNewCustomer">+ NEW CUSTOMER</span>
                        <span x-show="isNewCustomer">SEARCH EXISTING</span>
                    </button>
                </div>
                
                <div class="p-8 space-y-6">
                    <div x-show="!isNewCustomer" class="relative">
                        <input type="text" x-model="customerQuery" @input.debounce.300ms="searchCustomers()" placeholder="Search Customer (Name or Mobile)..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-blue-500/10 outline-none">
                        
                        <!-- Search Results -->
                        <div x-show="customerResults.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-2xl overflow-hidden" x-cloak>
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" class="p-4 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                                    <div class="font-bold text-slate-800" x-text="c.name"></div>
                                    <div class="text-xs text-slate-500" x-text="c.mobile"></div>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" name="customer_id" :value="customerId">
                    </div>

                    <div x-show="isNewCustomer" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>
                        <input type="text" name="customer_name" x-model="customerName" placeholder="Full Name" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <input type="tel" name="customer_mobile" x-model="customerMobile" placeholder="Mobile Number" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="md:col-span-2">
                             <input type="text" name="customer_address" x-model="customerAddress" placeholder="Full Address (Optional)" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center px-2">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        2. Item Selection & Dates
                    </h3>
                    <button type="button" @click="addProduct()" class="flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 text-xs font-bold transition-all uppercase tracking-widest shadow-sm">
                        + Add More Items
                    </button>
                </div>

                <template x-for="(product, index) in products" :key="product.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-8 relative group hover:shadow-xl transition-all duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3 relative">
                                <label class="text-xs font-black text-slate-400 tracking-widest uppercase">Search Product</label>
                                <input type="text" x-model="product.query" @input.debounce.300ms="searchProducts(product)" placeholder="Product Code / Name" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
                                
                                <!-- Product Search Results -->
                                <div x-show="product.results.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-xl shadow-2xl overflow-hidden" x-cloak>
                                    <template x-for="res in product.results" :key="res.id">
                                        <div @click="selectProduct(product, res)" class="p-4 hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors">
                                            <div class="font-bold text-slate-800" x-text="res.name"></div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase" x-text="res.code"></div>
                                        </div>
                                    </template>
                                </div>
                                <input type="hidden" :name="'products['+index+'][product_id]'" :value="product.selectedProductId">
                                <input type="hidden" :name="'products['+index+'][status]'" :value="product.status">
                            </div>
                            <div class="space-y-3">
                                <label class="text-xs font-black text-slate-400 tracking-widest uppercase">Select Size</label>
                                <select :name="'products['+index+'][size]'" x-model="product.selectedSize" @change="updatePrices(product, $event.target.value)" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                    <option value="">Select Size</option>
                                    <template x-for="s in product.availableStocks" :key="s.id">
                                        <option :value="s.size" x-text="`${s.size} (${s.qty} Avail.)`" :selected="s.size === product.selectedSize"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Date Selection for individual product -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                            <div class="space-y-3">
                                <label class="text-xs font-black text-blue-500 tracking-widest uppercase">Booking Start Date</label>
                                <input type="date" :name="'products['+index+'][from_date]'" x-model="product.fromDate" @change="checkAvailability(product)" class="w-full px-5 py-3.5 bg-blue-50/30 border border-blue-100 rounded-xl font-bold transition-all" :class="!product.available ? 'border-red-200 bg-red-50/30' : 'border-emerald-100 bg-emerald-50/20'">
                            </div>
                            <div class="space-y-3">
                                <label class="text-xs font-black text-blue-500 tracking-widest uppercase">Booking End Date</label>
                                <input type="date" :name="'products['+index+'][to_date]'" x-model="product.toDate" @change="checkAvailability(product)" class="w-full px-5 py-3.5 bg-blue-50/30 border border-blue-100 rounded-xl font-bold transition-all" :class="!product.available ? 'border-red-200 bg-red-50/30' : 'border-emerald-100 bg-emerald-50/20'">
                            </div>
                        </div>

                        <!-- Availability Display with Indicators -->
                        <div class="pt-4 flex items-center justify-between">
                            <div class="flex flex-wrap gap-2">
                                <div x-show="product.checking" class="flex items-center text-[10px] font-black uppercase text-blue-500 space-x-2">
                                    <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Syncing Inventory...</span>
                                </div>
                                <div x-show="!product.checking && product.selectedSize && product.fromDate && product.toDate">
                                    <div x-show="product.available" class="inline-flex items-center px-3 py-1 bg-emerald-500 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-md shadow-emerald-200 transition-all">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                        Date Available (<span x-text="product.remaining"></span> Left)
                                    </div>
                                    <div x-show="!product.available" class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-[10px] font-black rounded-lg uppercase tracking-widest animate-pulse shadow-md shadow-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                        Date Conflict / Locked
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" @click="product.showHistory = !product.showHistory" x-show="product.selectedSize" class="text-[10px] font-black uppercase text-blue-600 hover:text-blue-800 underline tracking-widest">
                                Check Booking History
                            </button>
                        </div>

                        <!-- Daily Availability List (User Requested) -->
                        <div x-show="product.days.length > 0" class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                             <h5 class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Availability Timeline</h5>
                             <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                 <template x-for="(d, idx) in product.days" :key="idx">
                                     <div class="px-3 py-1.5 rounded-lg border text-[10px] font-bold flex flex-col items-center justify-center transition-all"
                                          :class="d.available ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-red-50 border-red-100 text-red-600 shadow-inner'">
                                         <span x-text="d.date"></span>
                                         <span class="text-[8px] uppercase tracking-tighter" x-text="d.available ? 'Available' : 'Booked'"></span>
                                     </div>
                                 </template>
                             </div>
                        </div>

                        <!-- History Overlay (Glassmorphism) -->
                        <div x-show="product.showHistory" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute inset-0 z-40 bg-white/95 backdrop-blur-sm p-8 rounded-2xl overflow-y-auto border border-slate-100 shadow-2xl" id="history-modal" x-cloak>
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="font-black text-slate-800 uppercase tracking-widest text-xs">Recent Booking History</h4>
                                <button type="button" @click="product.showHistory = false" class="text-slate-400 hover:text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <template x-for="(h, i) in product.history" :key="i">
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 hover:bg-white hover:shadow-md transition-all">
                                        <div class="flex justify-between items-start">
                                            <span class="font-bold text-slate-800" x-text="h.customer"></span>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="'Booked at: ' + h.created_at"></span>
                                        </div>
                                        <div class="mt-2 flex items-center space-x-2 text-xs font-bold">
                                            <span class="text-blue-600" x-text="h.from"></span>
                                            <span class="text-slate-300">→</span>
                                            <span class="text-indigo-600" x-text="h.to"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="product.history.length === 0" class="text-center py-10">
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest italic">No prior bookings found</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                             <div class="space-y-3">
                                <label class="text-xs font-black text-slate-400 tracking-widest uppercase">Per Day Rent</label>
                                <div class="relative">
                                     <input type="number" :name="'products['+index+'][rent_price]'" x-model="product.rentPrice" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-lg text-slate-700 pr-16 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                                     <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black italic text-slate-300">/ Day</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-xs font-black text-slate-400 tracking-widest uppercase">Security Deposit</label>
                                <input type="number" :name="'products['+index+'][deposit]'" x-model="product.deposit" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-lg text-slate-700 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                            </div>
                        </div>

                        <!-- Duration display -->
                        <div x-show="calculateDays(product.fromDate, product.toDate) > 0" class="flex items-center space-x-2 text-[10px] font-black uppercase text-blue-600 tracking-[0.2em] italic">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                             <span>Duration: <span x-text="calculateDays(product.fromDate, product.toDate)"></span> Days</span>
                        </div>

                        <!-- Remove Button -->
                        <button type="button" @click="products = products.filter(p => p.id !== product.id)" 
                                x-show="products.length > 1"
                                class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-4 mt-6">
                    <label class="text-xs font-black text-slate-400 tracking-widest uppercase italic">Special Notes / Instructions</label>
                    <textarea name="note" rows="3" placeholder="Dry cleaning special request, altercations, etc..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:ring-4 focus:ring-blue-500/5 transition-all text-slate-700 font-medium">{{ $booking->note ?? '' }}</textarea>
                </div>

                <!-- NEW BILLING SECTION AT BOTTOM -->
                <div class="bg-[#1C2434] text-white rounded-3xl shadow-2xl p-10 mt-12 space-y-12 border border-white/5 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                         <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16.5c0 .38-.21.71-.53.88l-7.97 4.44c-.31.17-.69.17-1 0l-7.97-4.44c-.32-.17-.53-.5-.53-.88v-9c0-.38.21-.71.53-.88l7.97-4.44c.31-.17.69-.17 1 0l7.97 4.44c.32.17.53.5.53.88v9zM12 4.15L6.04 7.5 12 10.85l5.96-3.35L12 4.15zM5 15.91l6 3.35v-6.71l-6-3.35v6.71zm14 0v-6.71l-6 3.35v6.71l6-3.35z"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center border-b border-white/10 pb-8 gap-4">
                        <div class="text-center md:text-left">
                            <h3 class="text-2xl font-black tracking-widest uppercase">3. Final Billing Summary</h3>
                            <p class="text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] mt-1">Review Items & Confirm Transaction</p>
                        </div>
                        <div class="flex items-center space-x-6">
                             <div class="text-center">
                                 <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Rent</p>
                                 <p class="text-xl font-black">₹<span x-text="totalRent"></span></p>
                             </div>
                             <div class="h-8 w-px bg-white/10"></div>
                             <div class="text-center">
                                 <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Deposit</p>
                                 <p class="text-xl font-black">₹<span x-text="totalDeposit"></span></p>
                             </div>
                        </div>
                    </div>

                    <!-- Itemized Breakdown Table -->
                    <div class="relative z-10 overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] uppercase font-black tracking-widest text-slate-500 border-b border-white/10">
                                    <th class="py-4 px-2">Item Detail</th>
                                    <th class="py-4 px-2">Booking Dates</th>
                                    <th class="py-4 px-2 text-center">Days</th>
                                    <th class="py-4 px-2 text-right">Rent / Day</th>
                                    <th class="py-4 px-2 text-right">Deposit</th>
                                    <th class="py-4 px-2 text-right text-white">Item Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <template x-for="(p, i) in products" :key="p.id">
                                    <tr x-show="p.selectedProductId" class="text-sm">
                                        <td class="py-6 px-2">
                                            <div class="font-black text-white" x-text="p.selectedProductName"></div>
                                            <div class="text-[9px] font-bold text-slate-500 uppercase" x-text="'[' + p.selectedProductCode + '] Size: ' + p.selectedSize"></div>
                                        </td>
                                        <td class="py-6 px-2">
                                            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400">
                                                <span x-text="p.fromDate || '--'"></span>
                                                <span class="text-slate-600">→</span>
                                                <span x-text="p.toDate || '--'"></span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-2 text-center font-black text-blue-400" x-text="calculateDays(p.fromDate, p.toDate)"></td>
                                        <td class="py-6 px-2 text-right font-bold text-slate-400" x-text="'₹' + (p.rentPrice || 0)"></td>
                                        <td class="py-6 px-2 text-right font-bold text-slate-400" x-text="'₹' + (p.deposit || 0)"></td>
                                        <td class="py-6 px-2 text-right font-black text-white text-lg" x-text="'₹' + (calculateDays(p.fromDate, p.toDate) * (parseFloat(p.rentPrice) || 0))"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Final Totals & Actions -->
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-8 pt-8 border-t border-white/10">
                        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-amber-500 tracking-widest uppercase mb-2 block">Apply Discount</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-500">₹</span>
                                        <input type="number" name="discount" x-model="discount" class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-8 pr-4 text-xl font-black outline-none focus:bg-white/10 transition-all text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-blue-400 tracking-widest uppercase mb-1 block">Advance Paid Now (Default: Deposit)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-900 z-10">₹</span>
                                        <input type="number" name="advance_paid" x-model="advance" class="w-full bg-white border-2 border-transparent focus:border-blue-500 rounded-2xl py-6 pl-10 pr-4 text-3xl font-black text-[#1C2434] transition-all relative z-0">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/5 rounded-3xl p-8 flex flex-col items-center justify-center space-y-2 border border-white/5 relative z-10">
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest text-center">Balance at Dispatch <br><span class="text-blue-400/70 text-[8px]">(Auto-Applies Discount)</span></p>
                                <div class="relative flex items-center justify-center">
                                    <span class="font-black text-white text-4xl mr-1">₹</span>
                                    <input type="number" x-model="remainingBalance" class="w-[180px] bg-transparent border-b-2 border-white/20 focus:border-blue-400 py-1 text-6xl font-black text-white outline-none transition-all text-center">
                                </div>
                                <div class="mt-4 px-4 py-1.5 bg-blue-600/20 text-blue-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-500/20">
                                    Final Payable by Customer
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-4 justify-center">
                            <button type="submit" @click="isBooked = false" :disabled="hasUnavailability" :class="hasUnavailability ? 'opacity-50 cursor-not-allowed bg-slate-800' : 'bg-slate-700 hover:bg-slate-600'" class="w-full py-5 text-white rounded-2xl font-black uppercase tracking-widest transition-all text-sm shadow-xl active:scale-95">
                                Save as Draft
                            </button>
                            <button type="submit" @click="isBooked = true" :disabled="hasUnavailability" :class="hasUnavailability ? 'opacity-50 cursor-not-allowed bg-blue-800' : 'bg-blue-600 hover:bg-blue-700'" class="w-full py-6 text-white rounded-2xl font-black uppercase tracking-widest transition-all shadow-2xl shadow-blue-500/20 active:scale-95 group overflow-hidden relative">
                                <span class="relative z-10" x-text="hasUnavailability ? 'Check Inventory...' : 'Confirm & Block Dates'"></span>
                                <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-500"></div>
                            </button>
                            <p x-show="hasUnavailability && products.length > 0" class="text-[10px] text-red-400 text-center font-black uppercase tracking-widest animate-pulse">
                                Conflicts detected in inventory
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
