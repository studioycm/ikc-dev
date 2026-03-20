<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('breeding_inquiries', function (Blueprint $table) {
            // Link to legacy PrevBreedingHouse (mysql_prev) – no FK
            $table->unsignedBigInteger('prev_breeding_house_id')
                ->nullable()
                ->index()
                ->after('prev_breeding_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'prev_breeding_house_id',
            ]);
        });
    }
};
