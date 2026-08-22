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
        Schema::table('showtimes', function (Blueprint $table) {
            $table->decimal('vip_price', 10, 2)->default(150.00)->after('price');
            $table->decimal('student_price', 10, 2)->default(80.00)->after('vip_price');
            $table->decimal('child_price', 10, 2)->default(60.00)->after('student_price');
        });

        Schema::table('auditoriums', function (Blueprint $table) {
            $table->integer('vip_rows_count')->default(2)->after('seats_per_row');
            $table->decimal('base_price', 10, 2)->default(100.00)->after('total_seats');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->json('ticket_details')->nullable()->after('seat_numbers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropColumn(['vip_price', 'student_price', 'child_price']);
        });

        Schema::table('auditoriums', function (Blueprint $table) {
            $table->dropColumn(['vip_rows_count', 'base_price']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['ticket_details']);
        });
    }
};
