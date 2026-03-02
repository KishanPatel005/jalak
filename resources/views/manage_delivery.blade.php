@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    booking: {{ $booking->toJson() }},
    packingStates: {},
    balance: {{ $booking->balance_to_pay }},
    paymentAmount: {{ $booking->balance_to_pay }},
    init() {
        this.booking.items.forEach(item => {
            this.packingStates[item.id] = item.is_packed ? true : false;
        });
    },
    get isAllPacked() {
        return Object.values(this.packingStates).every(Boolean);
    },
    togglePacking(item) {
        let newState = this.packingStates[item.id];
        fetch('{{ route('deliveries.updatePacking', $booking->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: item.id,
                is_packed: newState ? 1 : 0
            })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) alert('Failed to update packing status');
        });
    },
    handleDispatch() {
        let remaining = this.balance - this.paymentAmount;
        if (remaining > 0) {
            Swal.fire({
                title: 'Partial Payment!',
                text: `₹${remaining.toLocaleString()} is still pending. Are you sure you want to dispatch this order without full payment?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#334155',
                confirmButtonText: 'Yes, Dispatch Anyway!',
                background: '#1e293b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$refs.dispatchForm.submit();
                }
            });
        } else {
            this.$refs.dispatchForm.submit();
        }
    }
}">
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('deliveries.index') }}" class="flex items-center text-slate-500 hover:text-slate-800 transition-colors font-black text-[10px] uppercase tracking-widest group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Deliveries
        </a>
        <div class="text-right">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">{{ $booking->invoice_no }}</h2>
            <p class="text-[10px] font-black uppercase text-indigo-600 tracking-widest mt-1">Delivery Checklist</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Customer & Order Summary -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">Customer Info</h3>
                <div class="space-y-3">
                    <p class="text-lg font-black text-slate-800 leading-tight">{{ $booking->customer->name }}</p>
                    <p class="text-sm font-bold text-slate-600 italic tracking-tight">{{ $booking->customer->mobile }}</p>
                    @if($booking->customer->address)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-[10px] font-medium text-slate-500 italic">
                            {{ $booking->customer->address }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">Packing Progress</h3>
                <div class="relative pt-1">
                    <div class="overflow-hidden h-3 mb-4 text-xs flex rounded-full bg-slate-100">
                        <div :style="`width: ${ (Object.values(packingStates).filter(Boolean).length / Object.keys(packingStates).length) * 100 }%`"
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-700 ease-out"></div>
                    </div>
                </div>
                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">
                    <span class="text-blue-500" x-text="Object.values(packingStates).filter(Boolean).length"></span> OF 
                    <span x-text="Object.keys(packingStates).length"></span> PRODUCTS PACKED
                </p>
            </div>
        </div>

        <!-- Packing Checklist -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-[#1C2434] text-white p-8 rounded-[40px] shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 relative">Verify & Pack Garments</h3>
                
                <div class="space-y-4 relative">
                    <template x-for="item in booking.items" :key="item.id">
                        <div class="flex items-center p-4 rounded-2xl transition-all duration-300 border-2"
                             :class="packingStates[item.id] ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-white/5 border-white/10 hover:border-white/20'">
                            <div class="flex-grow">
                                <p class="font-black text-sm tracking-tight" :class="packingStates[item.id] ? 'text-emerald-400' : 'text-white'" x-text="item.product.name"></p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-[9px] font-black bg-white/10 px-2 py-0.5 rounded uppercase tracking-widest text-slate-400">SIZE: <span class="text-white" x-text="item.size"></span></span>
                                    <span class="text-[9px] font-black bg-white/10 px-2 py-0.5 rounded uppercase tracking-widest text-slate-400">CODE: <span class="text-white" x-text="item.product.code"></span></span>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" 
                                       x-model="packingStates[item.id]" 
                                       @change="togglePacking(item)">
                                <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </template>
                </div>

                <div class="mt-10 pt-10 border-t border-white/5 space-y-6">
                    <div class="flex items-center justify-between p-6 bg-white/5 rounded-3xl border border-white/10">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Balance at Dispatch</p>
                            <p class="text-3xl font-black text-emerald-400 mt-1">₹{{ number_format($booking->balance_to_pay, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order Note</p>
                            <p class="text-xs font-medium text-white/50 italic max-w-[150px] truncate">{{ $booking->note ?? 'No special notes' }}</p>
                        </div>
                    </div>

                    <form x-ref="dispatchForm" action="{{ route('deliveries.dispatch', $booking->id) }}" method="POST" @submit.prevent="handleDispatch()">
                        @csrf
                        <div class="mb-6 p-6 bg-white/5 rounded-3xl border border-white/10 group focus-within:border-emerald-500/50 transition-all">
                             <div class="flex items-center justify-between mb-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Amount Collected Now</label>
                                <span x-show="balance - paymentAmount > 0" class="text-[9px] font-black text-red-500 uppercase tracking-widest animate-pulse">Remaining Unpaid: ₹<span x-text="(balance - paymentAmount).toLocaleString()"></span></span>
                                <span x-show="balance - paymentAmount <= 0" class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Full Payment Ready</span>
                             </div>
                             <div class="flex items-center">
                                <span class="text-2xl font-black text-white mr-2">₹</span>
                                <input type="number" name="payment_amount" step="0.01" 
                                       x-model="paymentAmount"
                                       :max="balance"
                                       class="w-full bg-transparent text-white text-3xl font-black outline-none placeholder:text-white/10" 
                                       placeholder="0.00">
                             </div>
                        </div>

                        <button type="submit" 
                                :disabled="!isAllPacked"
                                class="w-full py-5 rounded-[25px] font-black uppercase tracking-[0.2em] text-xs transition-all duration-500 shadow-2xl disabled:opacity-30 disabled:cursor-not-allowed"
                                :class="isAllPacked ? 'bg-emerald-500 text-white hover:bg-emerald-600 hover:scale-[1.02] shadow-emerald-500/20 active:scale-95' : 'bg-slate-700 text-slate-400'">
                            <span x-show="isAllPacked">CONFIRM & DISPATCH 🚀</span>
                            <span x-show="!isAllPacked">PACK ALL ITEMS FIRST 📦</span>
                        </button>
                    </form>
                </div>
            </div>

                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-[10px] font-bold text-blue-600 italic tracking-widest text-center uppercase">
                    Warning: Once dispatched, items will be moved to active rental status.
                </div>
            </div>
        </div>

        <!-- Detailed Billing Breakdown -->
        <div class="mt-12 bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-100 px-10 py-6">
                <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]">Detailed Billing Breakdown</h3>
            </div>
            
            <div class="p-10">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-100">
                            <th class="py-4">Product Details</th>
                            <th class="py-4">Booking Dates</th>
                            <th class="py-4 text-center">Days</th>
                            <th class="py-4 text-right">Rent Calc</th>
                            <th class="py-4 text-right">Rent Total</th>
                            <th class="py-4 text-right">Deposit</th>
                            <th class="py-4 text-right">Item Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php $totalItemsRent = 0; $totalItemsDeposit = 0; @endphp
                        @foreach($booking->items as $item)
                            @php 
                                $start = \Carbon\Carbon::parse($item->from_date);
                                $end = \Carbon\Carbon::parse($item->to_date);
                                $days = $start->diffInDays($end) + 1;
                                $itemRent = $days * $item->rent_price;
                                $totalItemsRent += $itemRent;
                                $totalItemsDeposit += $item->deposit_amount;
                            @endphp
                            <tr class="text-sm font-medium text-slate-700">
                                <td class="py-5">
                                    <div class="flex flex-col">
                                        <span class="font-black text-slate-800">{{ $item->product->name }}</span>
                                        <span class="text-[10px] uppercase font-bold text-slate-400">Code: {{ $item->product->code }} | Size: {{ $item->size }}</span>
                                    </div>
                                </td>
                                <td class="py-5">
                                    <span class="text-xs font-bold">{{ $item->from_date }} <span class="text-slate-300">to</span> {{ $item->to_date }}</span>
                                </td>
                                <td class="py-5 text-center font-black text-blue-600">{{ $days }}</td>
                                <td class="py-5 text-right font-bold text-slate-400 text-[10px]">{{ $days }} * ₹{{ number_format($item->rent_price, 2) }}</td>
                                <td class="py-5 text-right font-black text-slate-800">₹{{ number_format($itemRent, 2) }}</td>
                                <td class="py-5 text-right font-bold text-slate-500">₹{{ number_format($item->deposit_amount, 2) }}</td>
                                <td class="py-5 text-right font-black text-slate-800 bg-slate-50/50">₹{{ number_format($itemRent + $item->deposit_amount, 2) }}</td>
                            </tr>
@endforeach
                    </tbody>
                </table>

                <div class="mt-10 flex flex-col md:flex-row justify-end space-y-4 md:space-y-0 md:space-x-12 pt-8 border-t border-slate-100">
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Total Rent</span>
                        <span class="text-xl font-black text-slate-800">₹{{ number_format($totalItemsRent, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Total Deposit</span>
                        <span class="text-xl font-black text-slate-800">₹{{ number_format($totalItemsDeposit, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] font-black uppercase text-red-500 tracking-widest">Discount</span>
                        <span class="text-xl font-black text-red-500">- ₹{{ number_format($booking->discount, 2) }}</span>
                    </div>
                    <div class="flex flex-col items-end px-6 py-4 bg-emerald-50 rounded-3xl border border-emerald-100">
                        <span class="text-[10px] font-black uppercase text-emerald-600 tracking-widest">Booking Advance</span>
                        <span class="text-xl font-black text-emerald-600">₹{{ number_format($booking->advance_paid, 2) }}</span>
                        <span class="text-[8px] font-bold text-emerald-400 mt-0.5">{{ $booking->created_at->format('d M y - h:i A') }}</span>
                    </div>
                    @if($booking->dispatch_paid > 0)
                    <div class="flex flex-col items-end px-6 py-4 bg-blue-50 rounded-3xl border border-blue-100">
                        <span class="text-[10px] font-black uppercase text-blue-600 tracking-widest">Delivery Paid</span>
                        <span class="text-xl font-black text-blue-600">₹{{ number_format($booking->dispatch_paid, 2) }}</span>
                        <span class="text-[8px] font-bold text-blue-400 mt-0.5">{{ \Carbon\Carbon::parse($booking->dispatch_paid_at)->format('d M y - h:i A') }}</span>
                    </div>
                    @endif
                    <div class="flex flex-col items-end px-8 py-4 bg-[#1C2434] rounded-3xl text-white shadow-xl shadow-slate-200">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Balance Pending</span>
                        <span class="text-3xl font-black text-white">₹{{ number_format($booking->balance_to_pay, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
