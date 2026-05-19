@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-bold text-slate-800">Rent Management</h2>
        <p class="text-slate-500 mt-1">Track and manage product rentals and bookings.</p>
    </div>
    <a href="/manage-booking" class="flex items-center px-4 py-2 bg-[#1C2434] text-white rounded-lg hover:bg-slate-800 transition-colors shadow-md font-medium text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Booking
    </a>
</div>

<!-- Rent Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Today's Booked</h3>
            <p class="text-2xl font-black text-slate-800">{{ $todayBookedCount }} <span class="text-xs font-bold text-slate-400">Bookings</span></p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Total Booked</h3>
            <p class="text-2xl font-black text-slate-800">{{ $totalBookedCount }} <span class="text-xs font-bold text-slate-400">Lifetime</span></p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
    <form action="{{ route('rent.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Invoice, Name, Mobile..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500/20">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
            <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500/20 appearance-none">
                <option value="all">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="packed" {{ request('status') == 'packed' ? 'selected' : '' }}>Packed</option>
                <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Finished</option>
            </select>
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
            <a href="{{ route('rent.index') }}" class="bg-slate-100 text-slate-600 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Rent Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[10px] font-black uppercase tracking-[0.1em]">
                    <th class="px-6 py-5">Sr No</th>
                    <th class="px-6 py-5 text-center">Invoice No</th>
                    <th class="px-6 py-5">Customer</th>
                    <th class="px-6 py-5">Booking Range</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookings as $index => $booking)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-6 text-sm">
                        {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="text-xs font-black text-slate-800 tracking-tight bg-slate-100 px-3 py-1.5 rounded-lg">{{ $booking->invoice_no }}</span>
                    </td>
                    <td class="px-6 py-6">
                        <div class="text-[13px] font-black text-slate-800">{{ $booking->customer->name }}</div>
                        <div class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $booking->customer->mobile }}</div>
                    </td>
                    <td class="px-6 py-6">
                        @php $firstItem = $booking->items->first(); @endphp
                        @if($firstItem)
                            <div class="flex items-center space-x-2">
                                <span class="text-[11px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md tracking-tighter">{{ \Carbon\Carbon::parse($firstItem->from_date)->format('d M') }}</span>
                                <span class="text-slate-300 font-black">→</span>
                                <span class="text-[11px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md tracking-tighter">{{ \Carbon\Carbon::parse($firstItem->to_date)->format('d M') }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-6">
                        @php
                            $statusColors = [
                                'draft' => 'bg-slate-50 text-slate-500 border-slate-100',
                                'confirmed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'packed' => 'bg-purple-50 text-purple-600 border-purple-100',
                                'dispatched' => 'bg-teal-50 text-teal-600 border-teal-100',
                                'finished' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                            ];
                        @endphp
                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $statusColors[$booking->status] ?? $statusColors['draft'] }} italic shadow-sm">
                            {{ $booking->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end space-x-2 transition-all">
                            <a href="{{ route('bookings.invoice', $booking->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100" title="Download Invoice">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                            </a>
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm bg-white border border-slate-100" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Confirm deleting this record?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-all shadow-sm bg-white border border-slate-100" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-50 text-slate-300 rounded-3xl mb-4 border border-dashed border-slate-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest italic">No bookings found for this criteria.</p>
                        <a href="/rent" class="text-blue-500 font-bold text-[10px] uppercase tracking-widest hover:underline mt-4 inline-block">Clear Filters</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
