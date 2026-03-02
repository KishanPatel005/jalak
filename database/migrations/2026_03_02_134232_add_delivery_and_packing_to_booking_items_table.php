<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->boolean('is_packed')->default(false)->after('status');
            $table->timestamp('packed_at')->nullable()->after('is_packed');
            $table->boolean('is_dispatched')->default(false)->after('packed_at');
            $table->timestamp('dispatched_at')->nullable()->after('is_dispatched');
            $table->boolean('is_returned')->default(false)->after('dispatched_at');
            $table->timestamp('returned_at')->nullable()->after('is_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['is_packed', 'packed_at', 'is_dispatched', 'dispatched_at', 'is_returned', 'returned_at']);
        });
    }
};
