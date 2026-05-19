@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Dashboard Overview</h2>
        <p class="text-slate-500 mt-1">Welcome back to JALAK FASHION control center.</p>
    </div>
    <div class="flex space-x-3">
        <button class="flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Report
        </button>
        <button class="flex items-center px-4 py-2 bg-[#1C2434] text-white rounded-lg hover:bg-slate-800 transition-colors shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Tomorrow's Delivery -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-lg uppercase tracking-widest">Tomorrow</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Tomorrow's Delivery</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">
            {{ $tomorrowDeliveries }} 
            <span class="text-[11px] font-bold text-slate-400 normal-case ml-1 tracking-normal">
                ({{ $tomorrowPacked }} Packed / {{ $tomorrowUnpacked }} Unpacked)
            </span>
        </p>
    </div>

    <!-- Today's Delivery -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-lg uppercase tracking-widest">Today</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Today's Delivery</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">{{ $todayDeliveries }}</p>
    </div>

    <!-- Total Booking -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase tracking-widest">Lifetime</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Total Booking</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalBooking) }}</p>
    </div>

    <!-- Tomorrow's Returns -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2" /></svg>
            </div>
            <span class="text-[10px] font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-lg uppercase tracking-widest">Tomorrow</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Tomorrow's Returns</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">{{ $tomorrowReturns }}</p>
    </div>

    <!-- Today's Returns -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg uppercase tracking-widest">Today</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Today's Returns</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">{{ $todayReturns }}</p>
    </div>

    <!-- Total Delivered -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-slate-100 text-slate-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-3 py-1 rounded-lg uppercase tracking-widest">Archive</span>
        </div>
        <h3 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Total Delivered</h3>
        <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalDelivered) }}</p>
    </div>
</div>

<!-- Main Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Recent Transactions</h3>
            <a href="{{ route('rent.index') }}" class="text-[10px] font-black text-blue-600 hover:underline uppercase tracking-widest">View Master List</a>
        </div>
        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="pb-4 pr-4">Invoice</th>
                            <th class="pb-4 pr-4">Customer</th>
                            <th class="pb-4 pr-4 text-right">Amount</th>
                            <th class="pb-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentTransactions as $tx)
                        <tr class="text-xs group hover:bg-slate-50/50 transition-all cursor-default">
                            <td class="py-5 font-black text-slate-800 tracking-tight group-hover:text-blue-600">{{ $tx->invoice_no }}</td>
                            <td class="py-5 text-slate-600 font-bold">{{ $tx->customer->name }}</td>
                            <td class="py-5 font-black text-slate-800 text-right">₹{{ number_format($tx->grand_total, 2) }}</td>
                            <td class="py-5 text-right">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest italic border
                                    @if($tx->status == 'finished') border-emerald-100 bg-emerald-50 text-emerald-600
                                    @elseif($tx->status == 'dispatched') border-amber-100 bg-amber-50 text-amber-600
                                    @else border-blue-100 bg-blue-50 text-blue-600 @endif">
                                    {{ $tx->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Inventory Details -->
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-[40px] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Inventory Details</h3>
                <div class="p-2 bg-slate-50 rounded-xl">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Products</p>
                        <p class="text-xl font-black text-slate-800 mt-1">{{ number_format($totalProducts) }}</p>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 italic">Styles</span>
                </div>
                
                <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Quantity</p>
                        <p class="text-xl font-black text-slate-800 mt-1">{{ number_format($totalQty) }}</p>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 italic">Total Units</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
