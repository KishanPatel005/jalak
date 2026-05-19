<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size');
            $table->decimal('rent_price', 10, 2); // Per Day Rent
            $table->decimal('deposit_amount', 10, 2);
            $table->date('from_date');
            $table->date('to_date');
            $table->date('pickup_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('status')->default('pending'); // pending, out, returned
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
