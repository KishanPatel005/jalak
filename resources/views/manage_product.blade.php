@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center space-x-2 text-sm text-slate-400 mb-2">
        <a href="/products" class="hover:text-blue-600 transition-colors">Products</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600">Manage Product</span>
    </div>
    <h2 class="text-3xl font-bold text-slate-800">Manage Product</h2>
    <p class="text-slate-500 mt-1">Add or update product information in your catalog.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
        @csrf
        @isset($product)
            <input type="hidden" name="id" value="{{ $product->id }}">
        @endisset
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Gender</label>
                <select name="gender" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    <option value="male" @if(isset($product) && $product->gender === 'male') selected @endif>Male</option>
                    <option value="female" @if(isset($product) && $product->gender === 'female') selected @endif>Female</option>
                    <option value="children" @if(isset($product) && $product->gender === 'children') selected @endif>Children</option>
                </select>
            </div>
            
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Product Code</label>
                <input type="text" name="product_code" value="{{ $product->code ?? '' }}" placeholder="e.g. JF-101" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
            </div>
 
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-semibold text-slate-700">Product Name</label>
                <input type="text" name="product_name" value="{{ $product->name ?? '' }}" placeholder="Enter product name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
            </div>
        </div>

        <!-- Size & Stock Section -->
        <div class="space-y-4" x-data="{ sizeType: 'alpha' }">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <label class="text-sm font-bold text-[#1C2434] uppercase tracking-wider">Inventory & Pricing per Size</label>
                <div class="flex bg-slate-100 p-1 rounded-lg">
                    <button type="button" @click="sizeType = 'alpha'" :class="sizeType === 'alpha' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-3 py-1 text-xs font-bold rounded-md transition-all">ALPHA</button>
                    <button type="button" @click="sizeType = 'numeric'" :class="sizeType === 'numeric' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-3 py-1 text-xs font-bold rounded-md transition-all">NUMERIC</button>
                </div>
            </div>

            <!-- Alpha Sizes -->
            <div x-show="sizeType === 'alpha'" class="space-y-3">
                <div class="grid grid-cols-4 gap-4 px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    <div>Size</div>
                    <div>Qty</div>
                    <div>Rent Price</div>
                    <div>Deposit</div>
                </div>
                @php $alphaSizes = ['XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL']; @endphp
                @foreach($alphaSizes as $size)
                @php
                    $stock = isset($product) ? $product->stocks->firstWhere('size', $size) : null;
                @endphp
                <div class="grid grid-cols-4 gap-4 items-center p-3 border border-slate-100 rounded-xl bg-white hover:border-blue-200 transition-colors group">
                    <div class="text-xs font-black text-slate-700">{{ $size }}</div>
                    <input type="number" name="qty_{{ $size }}" value="{{ $stock->qty ?? '' }}" placeholder="0" min="0" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px]">₹</span>
                        <input type="number" name="rent_{{ $size }}" value="{{ $stock->rent_price ?? '' }}" placeholder="0" class="w-full pl-5 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px]">₹</span>
                        <input type="number" name="deposit_{{ $size }}" value="{{ $stock->deposit_amount ?? '' }}" placeholder="0" class="w-full pl-5 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Numeric Sizes -->
            <div x-show="sizeType === 'numeric'" class="space-y-3" x-cloak>
                <div class="grid grid-cols-4 gap-4 px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                    <div>Size</div>
                    <div>Qty</div>
                    <div>Rent Price</div>
                    <div>Deposit</div>
                </div>
                @php $numSizes = range(32, 48, 2); @endphp
                @foreach($numSizes as $size)
                @php
                    $stock = isset($product) ? $product->stocks->firstWhere('size', (string)$size) : null;
                @endphp
                <div class="grid grid-cols-4 gap-4 items-center p-3 border border-slate-100 rounded-xl bg-white hover:border-blue-200 transition-colors">
                    <div class="text-xs font-black text-slate-700">{{ $size }}</div>
                    <input type="number" name="qty_{{ $size }}" value="{{ $stock->qty ?? '' }}" placeholder="0" min="0" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px]">₹</span>
                        <input type="number" name="rent_{{ $size }}" value="{{ $stock->rent_price ?? '' }}" placeholder="0" class="w-full pl-5 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-slate-400 text-[10px]">₹</span>
                        <input type="number" name="deposit_{{ $size }}" value="{{ $stock->deposit_amount ?? '' }}" placeholder="0" class="w-full pl-5 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Product Image</label>
            @if(isset($product) && $product->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-32 h-32 object-cover rounded-xl border border-slate-200" alt="Current Image">
                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-widest">Current Image</p>
                </div>
            @endif
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="mb-2 text-sm text-slate-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-slate-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                    </div>
                    <input type="file" name="image" class="hidden" />
                </label>
            </div>
        </div>

        <!-- Note -->
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Note <span class="text-slate-400 font-normal">(Optional)</span></label>
            <textarea name="note" rows="3" placeholder="Add any special notes about this product..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">{{ $product->note ?? '' }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100">
            <a href="/products" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
            <button type="submit" class="px-8 py-2.5 bg-[#1C2434] text-white rounded-lg hover:bg-slate-800 transition-colors shadow-md font-bold text-sm">
                Save Product
            </button>
        </div>
    </form>
</div>
@endsection
