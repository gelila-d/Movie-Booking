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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditorium_id')->constrained('auditoriums')->onDelete('cascade');
            $table->string('row_letter', 5); // 'A', 'B', 'C'
            $table->integer('seat_number'); // 1, 2, 3
            $table->string('seat_label', 10); // 'A1', 'A2', 'B5'
            $table->string('type')->default('regular'); // 'regular', 'vip', 'accessible'
            $table->string('status')->default('active'); // 'active', 'maintenance'
            $table->timestamps();

            $table->unique(['auditorium_id', 'seat_label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
