<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('returned_at');
            $table->decimal('deposit_refunded', 10, 2)->default(0)->after('fine_amount');
            $table->string('return_condition')->default('good')->after('deposit_refunded'); // good, damaged, lost
            $table->text('return_note')->nullable()->after('return_condition');
        });
    }

    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'deposit_refunded', 'return_condition', 'return_note']);
        });
    }
};
