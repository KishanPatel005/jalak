<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifyCustomerTomorrowDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:customer-tomorrow-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify customer 1 day before delivery to take their shipment tomorrow';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService)
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $bookings = Booking::with(['customer', 'items.product'])->whereHas('items', function($q) use ($tomorrow) {
            $q->where('from_date', $tomorrow);
        })->get();

        if ($bookings->isEmpty()) {
            $this->info('No deliveries for customers tomorrow.');
            return;
        }

        foreach ($bookings as $booking) {
            $productCodes = $booking->items->where('from_date', $tomorrow)
                ->map(function($item) { return '*'.($item->product ? $item->product->name : 'Item').'* (Size: ' . $item->size . ')'; })
                ->implode(', ');

            $msg = "✨ *Jalak Fashion તરફથી રિમાઇન્ડર*\n\n";
            $msg .= "હેલો *{$booking->customer->name}*,\n\n";
            $msg .= "આ એક નમ્ર રિમાઇન્ડર છે કે તમારો ઓર્ડર (*{$booking->invoice_no}*) આવતીકાલે ડિલિવરી/પિકઅપ (*" . Carbon::tomorrow()->format('d M Y') . "*) માટે તૈયાર છે.\n\n";
            $msg .= "*વસ્તુઓ:* {$productCodes}\n\n";
            if ($booking->balance_to_pay > 0) {
                $msg .= "*બાકી રકમ:* ₹" . number_format($booking->balance_to_pay) . "\n\n";
            }
            $msg .= "અમે તમારા પ્રસંગ માટે ખૂબ ઉત્સાહિત છીએ! જો તમને કોઈ પ્રશ્ન હોય તો અમારો સંપર્ક કરો.\n\n---\n\n";
            $msg .= "✨ *Reminder from Jalak Fashion*\n\n";
            $msg .= "Hello *{$booking->customer->name}*,\n\n";
            $msg .= "This is a polite reminder that your order (*{$booking->invoice_no}*) is ready and scheduled for pickup/delivery tomorrow (*" . Carbon::tomorrow()->format('d M Y') . "*).\n\n";
            $msg .= "*Items:* {$productCodes}\n\n";
            if ($booking->balance_to_pay > 0) {
                $msg .= "*Pending Balance:* ₹" . number_format($booking->balance_to_pay) . "\n\n";
            }
            $msg .= "We are excited for your event! Please contact us if you have any last-minute questions.";

            try {
                $waService->sendText($booking->customer->mobile, trim($msg));
                $this->info("Successfully notified customer {$booking->customer->name} for tomorrow's delivery.");
            } catch (\Exception $e) {
                Log::error("Failed to notify customer for tomorrow's delivery: {$booking->customer->name} - " . $e->getMessage());
                $this->error("Failed to send message: " . $e->getMessage());
            }
        }
    }
}
