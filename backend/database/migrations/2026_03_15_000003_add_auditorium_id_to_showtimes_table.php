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
        if (!Schema::hasColumn('showtimes', 'auditorium_id')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->foreignId('auditorium_id')->nullable()->after('movie_id')->constrained('auditoriums')->onDelete('cascade');
            });
        } else {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->foreign('auditorium_id')->references('id')->on('auditoriums')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropForeign(['auditorium_id']);
            $table->dropColumn(['auditorium_id']);
        });
    }
};
