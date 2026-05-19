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
                            <!-- Multi-Type Downloads & WhatsApp -->
                            <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-100 mr-2">
                                <div class="flex border-r border-slate-200 pr-1 mr-1">
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
                                <a href="{{ route('bookings.whatsapp', ['id' => $booking->id, 'type' => $type != 'all' ? $type : '']) }}" class="p-2 text-green-600 hover:bg-white hover:shadow-sm rounded-lg transition-all" title="Send WhatsApp">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.7 17.8 69.4 27.2 106.2 27.2h.1c122.3 0 222-99.6 222-222 0-59.3-23-115.1-65.1-157.1zM223.9 446.7c-33.1 0-65.6-8.9-93.9-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.4-29.4-28.1-63.3-28.1-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-82.7 184.6-184.5 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.1-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.4-11.3 2.5-2.4 5.5-6.5 8.3-9.7 2.8-3.3 3.7-5.6 5.5-9.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.3 5.7 23.7 9.2 31.8 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
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
