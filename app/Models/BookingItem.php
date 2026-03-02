<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    protected $fillable = [
        'booking_id', 'product_id', 'size', 'rent_price', 'deposit_amount', 
        'from_date', 'to_date', 'status',
        'is_packed', 'packed_at', 'is_dispatched', 'dispatched_at', 
        'is_returned', 'returned_at',
        'fine_amount', 'deposit_refunded', 'return_condition', 'return_note'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
