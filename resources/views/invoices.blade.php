@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Invoice Center</h2>
    <p class="text-slate-500 mt-1 italic uppercase text-[10px] font-black tracking-widest">Master Repository for all transactions & documents</p>
</div>

<!-- Search & Fast Filters -->
<div class="bg-white p-6 rounded-[32px] shadow-sm border border-slate-100 mb-8">
    <form action="{{ route('invoices.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
        <div class="md:col-span-5">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Search Documents</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search Invoice No, Customer Name or Mobile..." 
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs font-bold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div class="md:col-span-4 flex items-center bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
            <a href="{{ route('invoices.index', ['type' => 'booking', 'search' => $search]) }}" 
                class="flex-1 py-2 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type == 'booking' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                Booking
            </a>
            <a href="{{ route('invoices.index', ['type' => 'delivery', 'search' => $search]) }}" 
                class="flex-1 py-2 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type == 'delivery' ? 'bg-white shadow-sm text-amber-600' : 'text-slate-400 hover:text-slate-600' }}">
                Delivery
            </a>
            <a href="{{ route('invoices.index', ['type' => 'return', 'search' => $search]) }}" 
                class="flex-1 py-2 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type == 'return' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                Return
            </a>
            <a href="{{ route('invoices.index', ['type' => 'all', 'search' => $search]) }}" 
                class="flex-1 py-2 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $type == 'all' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-400 hover:text-slate-600' }}">
                All
            </a>
        </div>

        <div class="md:col-span-3 flex space-x-3">
            <button type="submit" class="flex-1 bg-[#1C2434] text-white py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">
                Search
            </button>
            <a href="{{ route('invoices.index') }}" class="px-5 bg-slate-100 text-slate-500 py-3 rounded-2xl text-[10px] font-black uppercase flex items-center justify-center hover:bg-slate-200 transition-all">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Invoice List -->
<div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-8 py-6">Document Info</th>
                    <th class="px-8 py-6">Customer Details</th>
                    <th class="px-8 py-6">Current Phase</th>
                    <th class="px-8 py-6">Total Amount</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-slate-50/50 transition-all duration-300">
                    <td class="px-8 py-7">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tight">{{ $booking->invoice_no }}</span>
                            <span class="text-[9px] font-black text-slate-400 mt-1 tracking-widest">{{ $booking->created_at->format('d M, Y - h:i A') }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">{{ $booking->customer->name }}</span>
                            <span class="text-[9px] font-black text-slate-400 mt-0.5 tracking-widest">{{ $booking->customer->mobile }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        @php
                            $statusMap = [
                                'draft' => ['bg-slate-100 text-slate-600', 'Booking / Draft'],
                                'confirmed' => ['bg-blue-100 text-blue-600', 'Booking / Confirmed'],
                                'packed' => ['bg-purple-100 text-purple-600', 'Delivery / Packed'],
                                'dispatched' => ['bg-amber-100 text-amber-600', 'Delivery / Dispatched'],
                                'finished' => ['bg-emerald-100 text-emerald-600', 'Return / Settled'],
                                'cancelled' => ['bg-red-100 text-red-600', 'Cancelled'],
                            ];
                            $style = $statusMap[$booking->status] ?? ['bg-slate-100 text-slate-600', $booking->status];
                        @endphp
                        <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $style[0] }} italic">
                           {{ $style[1] }}
                        </span>
                    </td>
                    <td class="px-8 py-7">
                        <span class="text-sm font-black text-slate-800 italic">₹{{ number_format($booking->grand_total, 2) }}</span>
                    </td>
                    <td class="px-8 py-7 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <!-- Multi-Type Downloads -->
                            <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-100 mr-2">
                                <a href="{{ route('bookings.invoice', ['id' => $booking->id, 'type' => 'booking']) }}" class="p-2 text-blue-600 hover:bg-white hover:shadow-sm rounded-lg transition-all" title="Booking Invoice">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </a>
                                <a href="{{ route('bookings.invoice', ['id' => $booking->id, 'type' => 'delivery']) }}" class="p-2 text-amber-600 hover:bg-white hover:shadow-sm rounded-lg transition-all" title="Delivery Receipt">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </a>
                                <a href="{{ route('bookings.invoice', ['id' => $booking->id, 'type' => 'return']) }}" class="p-2 text-emerald-600 hover:bg-white hover:shadow-sm rounded-lg transition-all" title="Return Settlement">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2"/></svg>
                                </a>
                            </div>

                            <!-- Contextual Management Action -->
                            @if(in_array($booking->status, ['draft', 'confirmed']))
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center">
                                   Update Booking
                                </a>
                            @elseif(in_array($booking->status, ['packed', 'dispatched']))
                                <a href="{{ route('deliveries.manage', $booking->id) }}" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-amber-100 flex items-center">
                                    Manage Delivery
                                </a>
                            @elseif($booking->status === 'finished')
                                <a href="{{ route('returns.manage', $booking->id) }}" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 flex items-center">
                                    View Return
                                </a>
                            @else
                                <span class="text-[9px] font-bold text-slate-300 uppercase italic">Locked</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-24 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-[32px] flex items-center justify-center mb-6 border border-slate-100">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 class="text-slate-800 font-black text-sm uppercase tracking-widest">No matching invoices</h4>
                            <p class="text-slate-400 text-xs mt-2 italic font-bold">Try adjusting your filters or search keywords</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($bookings->hasPages())
    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
