@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    booking: {{ $booking->toJson() }},
    itemStates: {},
    init() {
        this.booking.items.forEach(item => {
            let due = new Date(item.to_date);
            let today = new Date();
            let lateDays = Math.max(0, Math.ceil((today - due) / (1000 * 60 * 60 * 24)));
            let suggestedFine = lateDays * (item.rent_price * 0.5); // Example: 50% of daily rent as fine per day

            this.itemStates[item.id] = {
                is_returned: item.is_returned ? true : false,
                fine_amount: item.is_returned ? item.fine_amount : suggestedFine,
                deposit_refunded: item.is_returned ? item.deposit_refunded : item.deposit_amount - suggestedFine,
                condition: item.return_condition || 'good',
                note: item.return_note || ''
            };
        });
    },
    get totals() {
        let totalFine = 0;
        let totalRefund = 0;
        let totalHeld = 0;
        
        Object.keys(this.itemStates).forEach(id => {
            totalFine += parseFloat(this.itemStates[id].fine_amount) || 0;
            totalRefund += parseFloat(this.itemStates[id].deposit_refunded) || 0;
            totalHeld += parseFloat(this.booking.items.find(i => i.id == id).deposit_amount) || 0;
        });

        return { totalFine, totalRefund, totalHeld };
    },
    updateItem(item) {
        let state = this.itemStates[item.id];
        fetch('{{ route('returns.updateItem', $booking->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: item.id,
                is_returned: state.is_returned ? 1 : 0,
                fine_amount: state.fine_amount,
                deposit_refunded: state.deposit_refunded,
                condition: state.condition,
                note: state.note
            })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) alert('Failed to update return status');
        });
    }
}">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <a href="{{ route('returns.index') }}" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-800 transition-all shadow-sm border border-slate-100 group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">{{ $booking->invoice_no }}</h2>
                <p class="text-[10px] font-black uppercase text-indigo-600 tracking-widest mt-1 italic">Security Deposit Settlement</p>
            </div>
        </div>
        <div class="text-right">
             <div class="flex items-center justify-end space-x-3">
                 <div class="px-5 py-2.5 bg-white border border-slate-100 rounded-2xl shadow-sm text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Deposit Held</p>
                    <p class="text-xl font-black text-slate-800 mt-1">₹{{ number_format($booking->items->sum('deposit_amount'), 2) }}</p>
                 </div>
             </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Item Return Toggles -->
        <div class="lg:col-span-8 space-y-6">
            @foreach($booking->items as $item)
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden transition-all duration-500 hover:shadow-xl hover:shadow-slate-100"
                 :class="itemStates[{{ $item->id }}].is_returned ? 'border-emerald-500/30' : 'border-slate-100'">
                
                <!-- Item Header -->
                <div class="px-10 py-8 flex items-center justify-between bg-white">
                    <div class="flex items-start space-x-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100 shadow-inner group">
                             <span class="text-2xl font-black text-slate-300 group-hover:scale-110 transition-transform">{{ substr($item->product->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 leading-tight">{{ $item->product->name }}</h3>
                            <div class="flex items-center space-x-3 mt-1.5">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-black rounded uppercase tracking-widest">SIZE: {{ $item->size }}</span>
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase tracking-widest">DUE: {{ \Carbon\Carbon::parse($item->to_date)->format('d M') }}</span>
                                @if(\Carbon\Carbon::parse($item->to_date)->isPast() && !$item->is_returned)
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-black rounded uppercase tracking-widest animate-pulse font-black italic">OVERDUE</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <label class="relative inline-flex items-center cursor-pointer group">
                             <input type="checkbox" class="sr-only peer" 
                                    x-model="itemStates[{{ $item->id }}].is_returned"
                                    @change="updateItem({{ $item->toJson() }})">
                             <div class="w-20 h-9 bg-slate-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-[44px] peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-7 after:w-7 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                             <span class="absolute left-3 text-[8px] font-black uppercase text-slate-400 peer-checked:opacity-0 transition-opacity">OUT</span>
                             <span class="absolute right-3 text-[8px] font-black uppercase text-white opacity-0 peer-checked:opacity-100 transition-opacity">IN</span>
                        </label>
                    </div>
                </div>

                <!-- Return Processing Panel (Only shows if toggle is IN) -->
                <div x-show="itemStates[{{ $item->id }}].is_returned" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-10 pb-10 pt-4 bg-emerald-50/20 border-t border-slate-50">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                        <!-- Condition & Notes -->
                        <div class="md:col-span-1 space-y-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Garment Condition</label>
                                <select x-model="itemStates[{{ $item->id }}].condition" 
                                        @change="updateItem({{ $item->toJson() }})"
                                        class="w-full bg-white border border-slate-100 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-sm appearance-none">
                                    <option value="good">✨ Perfect (Good)</option>
                                    <option value="damaged">💥 Damaged / Stain</option>
                                    <option value="lost">❌ Missing / Lost</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Internal Return Note</label>
                                <textarea x-model="itemStates[{{ $item->id }}].note" 
                                          @blur="updateItem({{ $item->toJson() }})"
                                          placeholder="Any specific stains or missing buttons?"
                                          class="w-full bg-white border border-slate-100 rounded-xl px-4 py-2.5 text-xs font-medium outline-none h-20 shadow-sm"></textarea>
                            </div>
                        </div>

                        <!-- Fine Calculation -->
                        <div class="md:col-span-1 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm space-y-4">
                             <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Deduction / Fine</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-red-500 text-xs">₹</span>
                                    <input type="number" 
                                           x-model="itemStates[{{ $item->id }}].fine_amount" 
                                           @input.debounce.500ms="updateItem({{ $item->toJson() }})"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-8 pr-4 py-3 text-lg font-black text-red-500 outline-none">
                                </div>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-2">Deducted from this item's deposit</p>
                             </div>
                        </div>

                        <!-- Refund Estimation -->
                        <div class="md:col-span-1 p-6 bg-[#1C2434] rounded-3xl text-white shadow-xl space-y-4 border border-white/5">
                             <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Net Refund for this Item</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-emerald-400 text-xs">₹</span>
                                    <input type="number" 
                                           x-model="itemStates[{{ $item->id }}].deposit_refunded" 
                                           @input.debounce.500ms="updateItem({{ $item->toJson() }})"
                                           class="w-full bg-white/5 border border-white/10 rounded-2xl pl-8 pr-4 py-3 text-lg font-black text-emerald-400 outline-none">
                                </div>
                                <p class="text-[8px] font-bold text-white/30 uppercase tracking-widest mt-2">Final amount given back to customer</p>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Right: Settlement Summary -->
        <div class="lg:col-span-4 sticky top-10">
            <div class="bg-white rounded-[40px] shadow-2xl border border-slate-100 p-10 space-y-8 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-10 opacity-5">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm0-2a6 6 0 100-12 6 6 0 000 12z"/></svg>
                </div>
                
                <h3 class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] relative">Refund Settlement</h3>
                
                <div class="space-y-4 relative">
                    <div class="flex justify-between items-center text-sm font-bold text-slate-500 italic">
                        <span>Original Security Deposit</span>
                        <span class="font-black text-slate-800">₹<span x-text="totals.totalHeld.toLocaleString()"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold text-red-500">
                        <span>Total Fines / Damage</span>
                        <span class="font-black">- ₹<span x-text="totals.totalFine.toLocaleString()"></span></span>
                    </div>
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex flex-col">
                             <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest leading-none">Final Refund Amount</span>
                             <span class="text-5xl font-black text-emerald-500 mt-2 tracking-tighter">₹<span x-text="totals.totalRefund.toLocaleString()"></span></span>
                        </div>
                    </div>
                </div>

                <div class="pt-10 border-t border-slate-100 space-y-6 relative">
                    <form action="{{ route('returns.finish', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full py-5 rounded-[25px] font-black uppercase tracking-[0.2em] text-xs transition-all duration-500 shadow-2xl border-2"
                                :class="Object.values(itemStates).every(i => i.is_returned) ? 'bg-[#1C2434] text-white border-[#1C2434] hover:bg-slate-800 hover:scale-[1.02] shadow-slate-200' : 'bg-slate-50 text-slate-300 border-slate-50 cursor-not-allowed'">
                            FINALIZE SETTLEMENT 🏦
                        </button>
                    </form>
                    <p class="text-[10px] font-bold text-slate-400 italic text-center px-4 leading-relaxed uppercase tracking-widest">
                        Verify all items are returned and deposits settled before closing this invoice.
                    </p>
                </div>
            </div>
            
            <div class="mt-6 p-6 bg-blue-50 border border-blue-100 rounded-3xl">
                 <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2 flex items-center">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Automatic Fine Suggestions
                 </h4>
                 <p class="text-[10px] font-medium text-blue-500 italic leading-relaxed">
                    The system automatically suggests a fine of 50% of the daily rent for each overdue day. You can manually override these amounts based on garment condition.
                 </p>
            </div>
        </div>
    </div>
</div>
@endsection
