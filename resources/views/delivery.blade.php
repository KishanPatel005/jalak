@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-end justify-between">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight text-uppercase uppercase">Delivery Management</h2>
        <p class="text-slate-500 mt-1 italic uppercase text-[10px] font-black tracking-widest">Schedule and monitor outbound packages</p>
    </div>
</div>

<!-- Delivery Status Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Today's Deliveries</h3>
            <p class="text-2xl font-black text-slate-800">{{ $todayDeliveries }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Tomorrow's Schedule</h3>
            <p class="text-2xl font-black text-slate-800">{{ $tomorrowDeliveries }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 border-l-4 border-l-emerald-400">
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Total Delivered (Today)</h3>
            <p class="text-2xl font-black text-slate-800">{{ $totalDeliveredToday }}</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
    <form action="{{ route('deliveries.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Order</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Invoice, Name, Phone..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">From Date</label>
            <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">To Date</label>
            <input type="date" name="to_date" value="{{ $toDate }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 bg-[#1C2434] text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                Filter
            </button>
            <a href="{{ route('deliveries.index') }}" class="bg-slate-100 text-slate-600 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Main Table -->
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 border-b border-slate-100">
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px]">Sr</th>
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px]">Invoice & Customer</th>
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px]">Items Status</th>
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px]">Financials</th>
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px]">Delivery Window</th>
                    <th class="px-8 py-5 font-black uppercase tracking-widest text-[10px] text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($bookings as $index => $booking)
                <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                    <td class="px-8 py-6 text-xs text-slate-400 font-bold italic">
                        {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tight">{{ $booking->invoice_no }}</span>
                            <span class="text-xs font-bold text-slate-500">{{ $booking->customer->name }}</span>
                            <span class="text-[9px] font-black text-blue-500 tracking-widest uppercase">{{ $booking->customer->mobile }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-2">
                            @foreach($booking->items as $item)
                                <div class="w-3 h-3 rounded-full shadow-sm border border-white {{ $item->is_dispatched ? 'bg-emerald-500' : ($item->is_packed ? 'bg-blue-500' : 'bg-slate-200') }}" 
                                     title="{{ $item->product->name }} [{{ $item->size }}]">
                                </div>
                            @endforeach
                            <span class="text-[9px] font-black text-slate-400 uppercase ml-2">
                                {{ $booking->items->where('is_packed', true)->count() }}/{{ $booking->items->count() }} Packed
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-xs font-black {{ $booking->balance_to_pay > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                ₹{{ number_format($booking->balance_to_pay, 2) }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Balance Pending</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        @php $firstItem = $booking->items->first(); @endphp
                        @if($firstItem)
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded uppercase tracking-tighter">{{ \Carbon\Carbon::parse($firstItem->from_date)->format('d M') }}</span>
                            <span class="text-slate-300 font-black">→</span>
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded uppercase tracking-tighter">{{ \Carbon\Carbon::parse($firstItem->to_date)->format('d M') }}</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end space-x-2 transition-all">
                            <a href="{{ route('bookings.invoice', $booking->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100" title="Download Invoice">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                            </a>
                            <a href="{{ route('deliveries.manage', $booking->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100" title="View Delivery Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </a>
                            @if($booking->status !== 'dispatched')
                                <a href="{{ route('deliveries.manage', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-[#1C2434] text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Ship Order
                                </a>
                            @else
                                <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-lg uppercase tracking-widest border border-emerald-100 italic">Dispatched</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 border border-dashed border-slate-200 rounded-3xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest italic">No matching deliveries found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
