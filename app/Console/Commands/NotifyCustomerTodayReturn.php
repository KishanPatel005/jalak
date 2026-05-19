<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifyCustomerTodayReturn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:customer-today-return';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify customer on return date to return products today';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService)
    {
        $today = Carbon::today()->toDateString();
        
        // We only want to message customers that have an item due today WHICH IS NOT RETURNED YET
        $bookings = Booking::with(['customer', 'items.product'])->whereHas('items', function($q) use ($today) {
            $q->where('to_date', $today)->where('is_returned', false);
        })->get();

        if ($bookings->isEmpty()) {
            $this->info('No pending returns from customers today.');
            return;
        }

        foreach ($bookings as $booking) {
            $productCodes = $booking->items->where('to_date', $today)->where('is_returned', false)
                ->map(function($item) { return '*'.($item->product ? $item->product->name : 'Item').'* (Size: ' . $item->size . ')'; })
                ->implode(', ');

            $msg = "🔔 *રિટર્ન રિમાઇન્ડર | Jalak Fashion*\n\n";
            $msg .= "હેલો *{$booking->customer->name}*,\n\n";
            $msg .= "અમે આશા રાખીએ છીએ કે તમારો પ્રસંગ સારો રહ્યો હશે!\n\n";
            $msg .= "આ એક નમ્ર રિમાઇન્ડર છે કે તમારો ભાડે લીધેલો સામાન (*{$booking->invoice_no}*) આજે જમા કરવાનો છે (*" . Carbon::today()->format('d M Y') . "*).\n\n";
            $msg .= "*પરત આપવાના બાકી કપડાં:*\n{$productCodes}\n\n";
            $msg .= "કૃપા કરીને બીજા કસ્ટમરના ઓર્ડર માટે શુવિધા થાય તે માટે સમયસર કપડાં પરત આપવા વિનંતી. આભાર! 🙏\n\n---\n\n";
            $msg .= "🔔 *Return Reminder | Jalak Fashion*\n\n";
            $msg .= "Hello *{$booking->customer->name}*,\n\n";
            $msg .= "We hope you had a fantastic time!\n\n";
            $msg .= "This is a gentle reminder that your rented items (*{$booking->invoice_no}*) are due for return today (*" . Carbon::today()->format('d M Y') . "*).\n\n";
            $msg .= "*Pending Items to Return:*\n{$productCodes}\n\n";
            $msg .= "Please return them to our store on time to help us prepare them for the next customer. Thank you! 🙏";

            try {
                $waService->sendText($booking->customer->mobile, trim($msg));
                $this->info("Successfully requested return from customer {$booking->customer->name} today.");
            } catch (\Exception $e) {
                Log::error("Failed to notify customer for today's return: {$booking->customer->name} - " . $e->getMessage());
                $this->error("Failed to send return message: " . $e->getMessage());
            }
        }
    }
}
