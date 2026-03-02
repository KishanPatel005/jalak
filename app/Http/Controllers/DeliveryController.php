<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = Booking::with(['customer', 'items.product'])->latest();

        // Search Filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date Filter based on first item from_date (Delivery Start Date)
        if ($fromDate && $toDate) {
            $query->whereHas('items', function($q) use ($fromDate, $toDate) {
                $q->whereBetween('from_date', [$fromDate, $toDate]);
            });
        }

        // Statistics
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $todayDeliveries = Booking::whereHas('items', function($q) use ($today) {
            $q->where('from_date', $today);
        })->count();

        $tomorrowDeliveries = Booking::whereHas('items', function($q) use ($tomorrow) {
            $q->where('from_date', $tomorrow);
        })->count();

        $totalDeliveredToday = Booking::where('status', 'dispatched')
            ->whereDate('dispatch_paid_at', $today)
            ->count();

        $bookings = $query->paginate(15)->withQueryString();

        return view('delivery', compact('bookings', 'fromDate', 'toDate', 'search', 'todayDeliveries', 'tomorrowDeliveries', 'totalDeliveredToday'));
    }

    public function manage($id)
    {
        $booking = Booking::with(['customer', 'items.product'])->findOrFail($id);
        return view('manage_delivery', compact('booking'));
    }

    public function updatePacking(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:booking_items,id',
            'is_packed' => 'required|boolean'
        ]);

        $item = BookingItem::findOrFail($request->item_id);
        $item->update([
            'is_packed' => $request->is_packed,
            'packed_at' => $request->is_packed ? now() : null
        ]);

        return response()->json(['success' => true]);
    }

    public function dispatch(Request $request, $id)
    {
        $request->validate([
            'payment_amount' => 'nullable|numeric|min:0'
        ]);

        $booking = Booking::with('items')->findOrFail($id);
        
        $dispatchPaid = $request->payment_amount ?? 0;
        $booking->update([
            'dispatch_paid' => $dispatchPaid,
            'dispatch_paid_at' => $dispatchPaid > 0 ? now() : null,
            'balance_to_pay' => $booking->balance_to_pay - $dispatchPaid,
            'status' => 'dispatched'
        ]);

        foreach ($booking->items as $item) {
            $item->update([
                'is_packed' => true,
                'is_dispatched' => true,
                'dispatched_at' => now(),
            ]);
        }

        return redirect()->route('deliveries.index')->with('success', "Order {$booking->invoice_no} dispatched successfully!");
    }
}
