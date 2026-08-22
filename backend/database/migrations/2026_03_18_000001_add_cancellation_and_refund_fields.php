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
            $table->string('status')->default('confirmed')->after('payment_method');
            $table->string('refund_status')->default('none')->after('status');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_status');
            $table->string('refund_ref')->nullable()->after('refund_amount');
            $table->timestamp('cancelled_at')->nullable()->after('refund_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['status', 'refund_status', 'refund_amount', 'refund_ref', 'cancelled_at']);
        });
    }
};
