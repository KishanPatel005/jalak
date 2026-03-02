<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status ?? 'dispatched';
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $query = Booking::with(['customer', 'items.product'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['dispatched', 'finished']);
        }

        if ($fromDate && $toDate) {
            $query->whereHas('items', function($q) use ($fromDate, $toDate) {
                $q->whereBetween('to_date', [$fromDate, $toDate]);
            });
        }

        $bookings = $query->get();

        // Add virtual attribute for total deposit held in this view
        $bookings->each(function($b) {
            $b->total_deposit_held = $b->items->sum('deposit_amount');
        });

        return view('return', compact('bookings', 'status', 'fromDate', 'toDate'));
    }

    public function manage($id)
    {
        $booking = Booking::with(['customer', 'items.product'])->findOrFail($id);
        return view('manage_return', compact('booking'));
    }

    public function updateItemReturn(Request $request, $bookingId)
    {
        $request->validate([
            'item_id' => 'required|exists:booking_items,id',
            'is_returned' => 'required|boolean',
            'fine_amount' => 'nullable|numeric|min:0',
            'deposit_refunded' => 'nullable|numeric|min:0',
            'condition' => 'required|string',
            'note' => 'nullable|string'
        ]);

        $item = BookingItem::findOrFail($request->item_id);
        $item->update([
            'is_returned' => $request->is_returned,
            'returned_at' => $request->is_returned ? now() : null,
            'fine_amount' => $request->fine_amount ?? 0,
            'deposit_refunded' => $request->deposit_refunded ?? 0,
            'return_condition' => $request->condition,
            'return_note' => $request->note
        ]);

        return response()->json(['success' => true]);
    }

    public function finish(Request $request, $id)
    {
        $booking = Booking::with('items')->findOrFail($id);
        
        $allReturned = $booking->items->every(fn($item) => $item->is_returned);
        
        if (!$allReturned) {
            return back()->with('error', 'All items must be returned before finishing the order.');
        }

        $booking->update([
            'status' => 'finished'
        ]);

        return redirect()->route('returns.index')->with('success', "Order #{$booking->invoice_no} has been successfully closed & returned.");
    }
}
