<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifyTomorrowsNotPacked extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:tomorrows-not-packed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a list of tomorrow\'s unsettled/unpacked deliveries to admin';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService)
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $bookings = Booking::with(['customer', 'items.product'])->whereHas('items', function($q) use ($tomorrow) {
            $q->where('from_date', $tomorrow)->where('is_packed', false);
        })->get();

        if ($bookings->isEmpty()) {
            $this->info('All of tomorrow\'s deliveries are already packed.');
            return;
        }

        $msg = "⚠️ *Attention: Unpacked Deliveries for Tomorrow (" . Carbon::tomorrow()->format('d M Y') . ")*\n\n";
        $msg .= "*Total Unpacked:* " . $bookings->count() . " Orders\n\n";

        foreach ($bookings as $booking) {
            $productCodes = $booking->items->where('from_date', $tomorrow)->where('is_packed', false)
                ->map(function($item) { return $item->product ? $item->product->code : ''; })
                ->filter()
                ->unique()
                ->implode(', ');
            
            $productStr = $productCodes ? " - {$productCodes}" : "";
            $msg .= "- *{$booking->invoice_no}* ({$booking->customer->name}){$productStr}\n";
        }

        try {
            $adminNumber = $waService->getAdminNumber();
            $waService->sendText($adminNumber, trim($msg));
            $this->info('Successfully sent unpacked deliveries to admin.');
        } catch (\Exception $e) {
            Log::error("Failed to send unpacked deliveries to admin: " . $e->getMessage());
            $this->error('Failed to send message: ' . $e->getMessage());
        }
    }
}
