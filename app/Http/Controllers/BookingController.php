<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function invoices(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type', 'all'); // all, booking, delivery, return

        $query = Booking::with('customer', 'items')->latest();

        if ($type === 'booking') {
            $query->whereNotIn('status', ['cancelled']);
        } elseif ($type === 'delivery') {
            $query->whereIn('status', ['packed', 'dispatched', 'finished']);
        } elseif ($type === 'return') {
            $query->where('status', 'finished');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(15)->appends($request->all());
        
        return view('invoices', compact('bookings', 'search', 'type'));
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $status = $request->get('status');

        $query = Booking::with('customer', 'items')->latest();

        // Status Filter
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        // Search Filter (Invoice, Name, Phone)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('mobile', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date Range Filter
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        // Stats for cards
        $todayBookedCount = Booking::whereDate('created_at', now()->today())->count();
        $totalBookedCount = Booking::count();

        // Paginate results
        $bookings = $query->paginate(15)->withQueryString();

        return view('rent', compact('bookings', 'todayBookedCount', 'totalBookedCount', 'search', 'fromDate', 'toDate', 'status'));
    }

    public function create()
    {
        return view('manage_booking');
    }

    public function searchCustomer(Request $request)
    {
        $query = $request->get('query');
        return Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('mobile', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('query');
        return Product::with('stocks')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();
    }

    public function checkAvailability(Request $request)
    {
        $productId = $request->product_id;
        $size = $request->size;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if (!$productId || !$size) {
            return response()->json(['available' => true, 'remaining' => 0, 'history' => []]);
        }

        // 1. Get total stock for this size
        $totalStock = ProductStock::where('product_id', $productId)
            ->where('size', $size)
            ->value('qty') ?? 0;

        // 2. Fetch history (always return if product/size set)
        $lastBookings = BookingItem::with('booking.customer')
            ->where('product_id', $productId)
            ->where('size', $size)
            ->whereNotIn('status', ['returned', 'cancelled'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'customer' => $item->booking->customer->name ?? 'Unknown',
                    'from' => $item->from_date,
                    'to' => $item->to_date,
                    'status' => $item->status,
                    'created_at' => $item->created_at->format('Y-m-d')
                ];
            });

        // 3. Detailed Day-by-Day Breakdown if dates are provided
        $dailyBreakdown = [];
        $available = true;
        $bookedQtyOverall = 0;
        $totalBilledDays = 0;
        
        if ($fromDate && $toDate) {
            $start = \Carbon\Carbon::parse($fromDate);
            $end = \Carbon\Carbon::parse($toDate);
            $totalBilledDays = $start->diffInDays($end) + 1;

            // BUFFER LOGIC: Block 1 day before and 1 day after
            $checkStart = $start->copy()->subDay();
            $checkEnd = $end->copy()->addDay();
            
            for ($date = $checkStart->copy(); $date->lte($checkEnd); $date->addDay()) {
                $currentDate = $date->format('Y-m-d');
                
                // Count bookings for THIS SPECIFIC DAY
                $dayBookedQty = BookingItem::where('product_id', $productId)
                    ->where('size', $size)
                    ->whereNotIn('status', ['returned', 'cancelled'])
                    ->where(function ($query) use ($currentDate) {
                        $query->where('from_date', '<=', $currentDate)
                              ->where('to_date', '>=', $currentDate);
                    })
                    ->count();
                
                $dayAvailable = ($totalStock - $dayBookedQty) > 0;
                if (!$dayAvailable) $available = false;
                
                $dailyBreakdown[] = [
                    'date' => $date->format('d M (D)'),
                    'is_buffer' => ($date->isSameDay($checkStart) || $date->isSameDay($checkEnd)),
                    'available' => $dayAvailable,
                    'remaining' => $totalStock - $dayBookedQty
                ];

                $bookedQtyOverall = max($bookedQtyOverall, $dayBookedQty);
            }
        }

        $remaining = $totalStock - $bookedQtyOverall;

        return response()->json([
            'available' => $available,
            'remaining' => $remaining,
            'total_stock' => $totalStock,
            'booked_qty' => $bookedQtyOverall,
            'total_billed_days' => $totalBilledDays,
            'history' => $lastBookings,
            'days' => $dailyBreakdown
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required_if:is_new_customer,false',
            'customer_name' => 'required_if:is_new_customer,true',
            'customer_mobile' => 'required_if:is_new_customer,true',
            'products' => 'required|array|min:1',
            'grand_total' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Handle Customer
            if ($request->is_new_customer === 'true' || $request->is_new_customer === true) {
                // Use firstOrCreate so if the mobile already exists, we reuse that customer
                // instead of crashing with a duplicate key error.
                $customer = Customer::firstOrCreate(
                    ['mobile' => $request->customer_mobile],  // search by mobile
                    [
                        'name'    => $request->customer_name,
                        'address' => $request->customer_address,
                    ]
                );

                // If customer already existed, update name/address in case they changed.
                if (!$customer->wasRecentlyCreated) {
                    $customer->update([
                        'name'    => $request->customer_name,
                        'address' => $request->customer_address ?? $customer->address,
                    ]);
                }
            } else {
                $customer = Customer::findOrFail($request->customer_id);
            }


            // 2. Generate Invoice No (BK-2026-001)
            $lastBooking = Booking::latest()->first();
            $nextId = $lastBooking ? $lastBooking->id + 1 : 1;
            $invoiceNo = 'BK-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            // 3. Create Booking
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'invoice_no' => $invoiceNo,
                'discount' => $request->discount ?? 0,
                'advance_paid' => $request->advance_paid ?? 0,
                'grand_total' => $request->grand_total,
                'balance_to_pay' => $request->grand_total - ($request->advance_paid ?? 0),
                'note' => $request->note,
                'status' => $request->status ?? 'draft',
            ]);

            // 4. Create Booking Items
            foreach ($request->products as $item) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'rent_price' => $item['rent_price'],
                    'deposit_amount' => $item['deposit'],
                    'from_date' => $item['from_date'],
                    'to_date' => $item['to_date'],
                    'status' => 'pending',
                ]);
            }

            // 5. WhatsApp Integration
            try {
                $waService = app(\App\Services\WhatsAppService::class);
                
                // Load PDF
                $pdf = Pdf::loadView('invoice', ['booking' => $booking->load('customer', 'items.product'), 'type' => 'booking']);
                $pdfContent = $pdf->output();
                $fileName = "{$booking->invoice_no}.pdf";

                // Prepare Item Details for Message
                $itemDetails = "";
                foreach ($booking->items as $item) {
                    $itemDetails .= "- *{$item->product->name}* ({$item->size})\n";
                }

                $pickupDate = \Carbon\Carbon::parse($booking->items->first()->from_date)->subDay()->format('d M Y');
                $returnDate = \Carbon\Carbon::parse($booking->items->first()->to_date)->format('d M Y');
                $bookedDates = \Carbon\Carbon::parse($booking->items->first()->from_date)->format('d M Y') . " to " . $returnDate;

                // Customer Message
                $customerMsg = "હેલો *{$customer->name}*,\n\nતમારું બુકિંગ *{$booking->invoice_no}* કન્ફર્મ થઈ ગયું છે! 🎉\n\n*બુકિંગ વિગતો:*\n{$itemDetails}\n*કુલ રકમ:* ₹" . number_format($booking->grand_total) . "\n*એડવાન્સ પેમેન્ટ:* ₹" . number_format($booking->advance_paid) . "\n*બાકી રકમ:* ₹" . number_format($booking->balance_to_pay) . "\n\n*મહત્વપૂર્ણ માહિતી:*\n- પિકઅપ તારીખ: *{$pickupDate}* સવારે *11:30 વાગ્યે*\n- વપરાશની તારીખ: *{$bookedDates}*\n- જમા કરવાની તારીખ: *{$returnDate}* સવારે *11:30 વાગ્યે*\n\nજલક ફેશન પસંદ કરવા બદલ આભાર!\n\n---\n\nHello *{$customer->name}*,\n\nYour booking *{$booking->invoice_no}* is confirmed! 🎉\n\n*Order Details:*\n{$itemDetails}\n*Total:* ₹" . number_format($booking->grand_total) . "\n*Advance Paid:* ₹" . number_format($booking->advance_paid) . "\n*Balance:* ₹" . number_format($booking->balance_to_pay) . "\n\n*Important Instructions:*\n- Pickup on *{$pickupDate}* at *11:30 AM*\n- Booked Dates: *{$bookedDates}*\n- Return on *{$returnDate}* at *11:30 AM*\n\nThank you for choosing *Jalak Fashion*!";

                $waService->sendPdfContent($customer->mobile, $pdfContent, $fileName, $customerMsg);

                // Admin Message
                $adminMsg = "✨ *New Booking Received*\n\n*Invoice:* {$booking->invoice_no}\n*Customer:* {$customer->name} ({$customer->mobile})\n*Products:*\n{$itemDetails}\n*Total:* ₹" . number_format($booking->grand_total) . "\n\nCustomer will pickup on *{$pickupDate}*, use on *{$booking->items->first()->from_date}*, and return on *{$returnDate}*.";
                
                $waService->sendPdfContent($waService->getAdminNumber(), $pdfContent, $fileName, $adminMsg);

            } catch (\Exception $e) {
                Log::error("WhatsApp automated send failed: " . $e->getMessage());
            }

            return redirect('/rent')->with('success', "Booking {$invoiceNo} created successfully!");
        });
    }

    public function edit($id)
    {
        $booking = Booking::with(['customer', 'items.product.stocks'])->findOrFail($id);
        return view('manage_booking', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required',
            'products' => 'required|array|min:1',
            'grand_total' => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $booking = Booking::findOrFail($id);
            $customer = Customer::findOrFail($request->customer_id);

            // 1. Update Customer (if changed)
            if ($request->is_new_customer === 'false' || $request->is_new_customer === false) {
                 $customer->update([
                    'name' => $request->customer_name ?? $customer->name,
                    'mobile' => $request->customer_mobile ?? $customer->mobile,
                    'address' => $request->customer_address ?? $customer->address,
                ]);
            }

            // 2. Update Booking
            $booking->update([
                'customer_id' => $customer->id,
                'discount' => $request->discount ?? 0,
                'advance_paid' => $request->advance_paid ?? 0,
                'grand_total' => $request->grand_total,
                'balance_to_pay' => $request->grand_total - ($request->advance_paid ?? 0),
                'note' => $request->note,
                'status' => $request->status ?? $booking->status,
            ]);

            // 3. Sync Booking Items (Delete old, add new)
            $booking->items()->delete();
            foreach ($request->products as $item) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'rent_price' => $item['rent_price'],
                    'deposit_amount' => $item['deposit'],
                    'from_date' => $item['from_date'],
                    'to_date' => $item['to_date'],
                    'status' => $item['status'] ?? 'pending',
                ]);
            }

            return redirect('/rent')->with('success', "Booking {$booking->invoice_no} updated successfully!");
        });
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->items()->delete();
        $booking->delete();
        return redirect('/rent')->with('success', "Booking deleted successfully!");
    }

    public function downloadInvoice($id, Request $request)
    {
        $booking = Booking::with(['customer', 'items.product'])->findOrFail($id);
        $type = $request->get('type'); // Force a specific type if requested
        
        $pdf = Pdf::loadView('invoice', compact('booking', 'type'));
        
        $filename = "{$booking->invoice_no}";
        if ($type) $filename .= "_{$type}";
        
        return $pdf->download("{$filename}.pdf");
    }

    public function sendWhatsapp($id, Request $request, \App\Services\WhatsAppService $waService)
    {
        $booking = Booking::with(['customer', 'items.product'])->findOrFail($id);
        $type = $request->get('type');
        
        // 1. Generate PDF
        $pdf = Pdf::loadView('invoice', compact('booking', 'type'));
        $pdfContent = $pdf->output();
        
        $filename = "{$booking->invoice_no}";
        if ($type) $filename .= "_{$type}";
        $filename .= ".pdf";

        // 2. Prepare Caption
        $label = "Invoice";
        if ($type === 'delivery') $label = "Delivery Challan";
        if ($type === 'return') $label = "Return Settlement";

        $caption = "હેલો *{$booking->customer->name}*,\n\nઅહીં તમારું *{$label}* છે.\n\n*ઇનવોઇસ નમ્બર:* {$booking->invoice_no}\n*કુલ રકમ:* ₹" . number_format($booking->grand_total) . "\n*બાકી રકમ:* ₹" . number_format($booking->balance_to_pay) . "\n\nજલક ફેશન પસંદ કરવા બદલ આભાર!\n\n---\n\nHello *{$booking->customer->name}*,\n\nHere is your *{$label}* from *Jalak Fashion*.\n\n*Invoice No:* {$booking->invoice_no}\n*Total Amount:* ₹" . number_format($booking->grand_total) . "\n*Balance:* ₹" . number_format($booking->balance_to_pay) . "\n\nThank you for choosing us!";

        // 3. Send via WhatsApp
        $result = $waService->sendPdfContent($booking->customer->mobile, $pdfContent, $filename, $caption);

        if (isset($result['error']) || (isset($result['status']) && $result['status'] === 'error')) {
            $msg = $result['message'] ?? ($result['response']['message'] ?? 'Unknown Error');
            return back()->with('error', 'WhatsApp Failed: ' . $msg);
        }

        return back()->with('success', "WhatsApp {$label} sent to {$booking->customer->mobile} successfully!");
    }
}
