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
            // Link to legacy PrevUser as breeder (mysql_prev) – no FK
            $table->unsignedBigInteger('prev_breeder_id')
                ->nullable()
                ->after('prev_breeding_house_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'prev_breeder_id',
            ]);
        });
    }
};
