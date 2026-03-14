<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifyTomorrowsDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:tomorrows-deliveries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a list of tomorrow\'s deliveries and special notes to admin';

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
            $this->info('No deliveries for tomorrow.');
            return;
        }

        $msg = "📦 *Tomorrow's Deliveries (" . Carbon::tomorrow()->format('d M Y') . ")*\n\n";
        $msg .= "*Total Deliveries:* " . $bookings->count() . "\n\n";

        foreach ($bookings as $booking) {
            $productCodes = $booking->items->where('from_date', $tomorrow)
                ->map(function($item) { return $item->product ? $item->product->code : ''; })
                ->filter()
                ->unique()
                ->implode(', ');
            
            $productStr = $productCodes ? " - {$productCodes}" : "";
            $msg .= "- *{$booking->invoice_no}* ({$booking->customer->name}){$productStr}\n";
            if (!empty($booking->note)) {
                $msg .= "  *Note:* {$booking->note}\n";
            }
            $msg .= "\n";
        }

        try {
            $adminNumber = $waService->getAdminNumber();
            $waService->sendText($adminNumber, trim($msg));
            $this->info('Successfully sent tomorrow\'s deliveries to admin.');
        } catch (\Exception $e) {
            Log::error("Failed to send tomorrow's deliveries to admin: " . $e->getMessage());
            $this->error('Failed to send message: ' . $e->getMessage());
        }
    }
}
