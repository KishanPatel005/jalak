<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = ['product_id', 'size', 'qty', 'rent_price', 'deposit_amount'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
