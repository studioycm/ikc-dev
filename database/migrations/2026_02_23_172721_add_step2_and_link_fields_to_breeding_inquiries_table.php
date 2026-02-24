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
            // Link to legacy PrevBreeding (mysql_prev) – no FK
            $table->unsignedBigInteger('prev_breeding_id')
                ->nullable()
                ->index()
                ->after('male_sagir_id');

            // Step 2 repeater (rights transfer / breeder / kennel)
            $table->json('breeding_rights')
                ->nullable()
                ->after('breeding_date');

            // Step 4 fields (currently in form but missing in DB)
            $table->string('review_type')
                ->nullable()
                ->after('puppies');

            $table->string('payment_type')
                ->nullable()
                ->after('review_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'prev_breeding_id',
                'breeding_rights',
                'review_type',
                'payment_type',
            ]);
        });
    }
};
