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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('genre')->nullable()->after('description');
            $table->integer('duration')->nullable()->after('genre'); // in minutes
            $table->string('rating')->default('PG-13')->after('duration'); // PG-13, R, PG, G
            $table->string('trailer_url')->nullable()->after('rating');
            $table->boolean('is_published')->default(true)->after('trailer_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['genre', 'duration', 'rating', 'trailer_url', 'is_published']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active']);
        });
    }
};
