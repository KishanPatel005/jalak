@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Products</h2>
        <p class="text-slate-500 mt-1">Manage your apparel inventory and stock levels.</p>
    </div>
    <a href="/manage-product" class="flex items-center px-4 py-2 bg-[#1C2434] text-white rounded-lg hover:bg-slate-800 transition-colors shadow-md font-medium text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New Product
    </a>
</div>

<!-- Product Table -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
        </div>
        <div class="flex items-center space-x-3">
            <select class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none">
                <option>All Categories</option>
                <option>Male</option>
                <option>Female</option>
                <option>Children</option>
            </select>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Sr No</th>
                    <th class="px-6 py-4 font-semibold">Product</th>
                    <th class="px-6 py-4 font-semibold">Code</th>
                    <th class="px-6 py-4 font-semibold">Category</th>
                    <th class="px-6 py-4 font-semibold">Sizes & Qty</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $index => $product)
                <tr class="hover:bg-slate-50 transition-colors group text-sm italic italic">
                    <td class="px-6 py-4 text-slate-600 font-bold tracking-widest">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=E0E7FF&color=4338CA&bold=true' }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shadow-sm" alt="Product">
                            <span class="font-bold text-slate-800 tracking-tight">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-black text-slate-600 tracking-tighter">{{ $product->code }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-full 
                            {{ $product->gender === 'female' ? 'bg-pink-50 text-pink-600' : ($product->gender === 'male' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600') }}">
                            {{ $product->gender }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->stocks as $stock)
                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-50 border border-slate-100 text-slate-700 text-[10px] font-black rounded uppercase tracking-widest">
                                {{ $stock->size }} - {{ $stock->qty }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-all" title="Rent">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                            <a href="{{ route('products.edit', $product->id) }}" class="p-1.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-all" title="Update">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition-all" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight">No Products Found</h3>
                            <p class="text-xs text-slate-500 mt-1 italic italic">Start by adding your first garment to the catalog.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-50 flex items-center justify-between">
        <p class="text-sm text-slate-500">Showing 3 of 150 products</p>
        <div class="flex space-x-2">
            <button class="px-3 py-1 border border-slate-200 rounded text-slate-400 cursor-not-allowed text-sm">Previous</button>
            <button class="px-3 py-1 bg-[#1C2434] text-white rounded text-sm shadow-sm">Next</button>
        </div>
    </div>
</div>
@endsection
