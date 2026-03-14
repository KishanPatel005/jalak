<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // WhatsApp Integration
        try {
            $waService = app(\App\Services\WhatsAppService::class);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice', ['booking' => $booking->load('customer', 'items.product'), 'type' => 'return']);
            $pdfContent = $pdf->output();
            $fileName = "{$booking->invoice_no}_return.pdf";

            $totalFine = $booking->items->sum('fine_amount');
            $totalRefund = $booking->items->sum('deposit_refunded');

            $customerMsg = "✅ *સામાન પરત જમા થઈ ગયેલ છે*\n\nહેલો *{$booking->customer->name}*,\n\nતમારું ઇનવોઇસ *{$booking->invoice_no}* નું રિટર્ન સેટલમેન્ટ થઈ ગયું છે. \n\n*કુલ દંડ:* ₹" . number_format($totalFine) . "\n*ડિપોઝિટ પરત આપી:* ₹" . number_format($totalRefund) . "\n\nજલક ફેશન પસંદ કરવા બદલ આભાર! અમે આશા રાખીએ છીએ કે તમે ફરીથી આવડશો.\n\n---\n\n✅ *Return Settled*\n\nHello *{$booking->customer->name}*,\n\nYour return for *{$booking->invoice_no}* is settled. \n\n*Total Fine:* ₹" . number_format($totalFine) . "\n*Security Refunded:* ₹" . number_format($totalRefund) . "\n\nThank you for choosing *Jalak Fashion*! We hope to see you again soon.";
            $waService->sendPdfContent($booking->customer->mobile, $pdfContent, $fileName, $customerMsg);

            $adminMsg = "🔄 *Return Completed*\n\n*Invoice:* {$booking->invoice_no}\n*Customer:* {$booking->customer->name}\n*Fine Collected:* ₹" . number_format($totalFine) . "\n*Refunded:* ₹" . number_format($totalRefund);
            $waService->sendPdfContent($waService->getAdminNumber(), $pdfContent, $fileName, $adminMsg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("WhatsApp return send failed: " . $e->getMessage());
        }

        return redirect()->route('returns.index')->with('success', "Order #{$booking->invoice_no} has been successfully closed & returned.");
    }
}

