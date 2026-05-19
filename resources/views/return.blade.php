@extends('layouts.admin')

@section('content')
<div class="mb-8 flex items-end justify-between">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight text-uppercase">Return Center</h2>
        <p class="text-slate-500 mt-1 italic uppercase text-[10px] font-black tracking-widest">Process returns and settle security deposits</p>
    </div>
    <div class="flex items-center space-x-4">
        <form action="{{ route('returns.index') }}" method="GET" class="flex items-end space-x-3 bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">View Status</label>
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-black uppercase outline-none focus:ring-2 focus:ring-blue-500/20 w-32">
                    <option value="dispatched" {{ $status == 'dispatched' ? 'selected' : '' }}>Current Out</option>
                    <option value="finished" {{ $status == 'finished' ? 'selected' : '' }}>History</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Records</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Due From</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-bold outline-none">
            </div>
            <div class="flex items-center pb-2 text-slate-300 font-black">→</div>
            <div>
                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Due To</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-[10px] font-bold outline-none">
            </div>
            <button type="submit" class="bg-[#1C2434] text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                Filter
            </button>
        </form>
    </div>
</div>

<!-- Return Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Active Rentals</h3>
            <p class="text-2xl font-black text-slate-800">{{ $bookings->count() }}</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-red-50 text-red-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Overdue Today</h3>
            <p class="text-2xl font-black text-slate-800">
                {{ $bookings->filter(fn($b) => $b->items->some(fn($i) => \Carbon\Carbon::parse($i->to_date)->isPast() && !$i->is_returned))->count() }}
            </p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Partially Ret.</h3>
            <p class="text-2xl font-black text-slate-800">
                {{ $bookings->filter(fn($b) => $b->items->some(fn($i) => $i->is_returned) && $b->items->some(fn($i) => !$i->is_returned))->count() }}
            </p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Refunds Pending</h3>
            <p class="text-2xl font-black text-slate-800">₹{{ number_format($bookings->sum('total_deposit_held'), 2) }}</p>
        </div>
    </div>
</div>

<!-- Return List -->
<div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-10 py-6">Order & Customer</th>
                    <th class="px-10 py-6">Items Status</th>
                    <th class="px-10 py-6">Due Dates</th>
                    <th class="px-10 py-6">Deposit Held</th>
                    <th class="px-10 py-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-slate-50/50 transition-all duration-300">
                    <td class="px-10 py-8">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tight">{{ $booking->invoice_no }}</span>
                            <span class="text-xs font-bold text-slate-500 group-hover:text-blue-600 transition-colors">{{ $booking->customer->name }}</span>
                            <span class="text-[9px] font-black text-slate-400 mt-0.5 tracking-[0.1em]">{{ $booking->customer->mobile }}</span>
                        </div>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex -space-x-2">
                            @foreach($booking->items as $item)
                                <div class="w-8 h-8 rounded-xl border-2 border-white flex items-center justify-center text-[10px] font-black shadow-sm group relative
                                     {{ $item->is_returned ? 'bg-emerald-500 text-white' : (\Carbon\Carbon::parse($item->to_date)->isPast() ? 'bg-red-500 text-white animate-pulse' : 'bg-blue-100 text-blue-600') }}"
                                     title="{{ $item->product->name }} [{{ $item->size }}]">
                                    {{ substr($item->product->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[9px] font-black text-slate-400 uppercase mt-2 italic tracking-widest">
                            {{ $booking->items->where('is_returned', true)->count() }} / {{ $booking->items->count() }} Returned
                        </p>
                    </td>
                    <td class="px-10 py-8">
                        @php $lastDue = \Carbon\Carbon::parse($booking->items->max('to_date')); @endphp
                        <div class="flex flex-col">
                            <span class="text-xs font-black {{ $lastDue->isPast() ? 'text-red-500' : 'text-slate-800' }}">
                                {{ $lastDue->format('d M, Y') }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">Final Due Date</span>
                        </div>
                    </td>
                    <td class="px-10 py-8">
                        <span class="text-sm font-black text-slate-800 italic">
                            ₹{{ number_format($booking->items->sum('deposit_amount'), 2) }}
                        </span>
                    </td>
                    <td class="px-10 py-8 text-right">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('bookings.invoice', $booking->id) }}" class="p-2.5 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100" title="Download Invoice">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                            </a>
                            @if($booking->status == 'finished')
                                <span class="px-5 py-2.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-2xl uppercase tracking-widest border border-emerald-100 italic">
                                    Settled
                                </span>
                            @else
                                 <a href="{{ route('returns.manage', $booking->id) }}" class="inline-flex items-center px-6 py-3 bg-[#1C2434] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-95 group">
                                    <svg class="w-3 h-3 mr-2 transform group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4 2 4-2 4 2"/></svg>
                                    Process Return
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center mb-4 border border-slate-100">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4 2 4-2 4 2"/></svg>
                            </div>
                            <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.2em] italic">No active rentals found matching your records</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
