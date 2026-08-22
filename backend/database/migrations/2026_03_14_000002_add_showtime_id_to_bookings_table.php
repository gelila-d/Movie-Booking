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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('showtime_id')->nullable()->after('movie_id')->constrained()->onDelete('cascade');
            $table->decimal('total_price', 8, 2)->nullable()->after('seats_booked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['showtime_id']);
            $table->dropColumn(['showtime_id', 'total_price']);
        });
    }
};
