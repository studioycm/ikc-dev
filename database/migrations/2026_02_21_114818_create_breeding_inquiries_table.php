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
        Schema::create('breeding_inquiries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('prev_user_id')->index();

            $table->unsignedInteger('female_sagir_id')->index();
            $table->unsignedInteger('male_sagir_id')->nullable()->index();

            $table->string('litter_report_name')->nullable();

            $table->date('breeding_date')->nullable();
            $table->date('birthing_date')->nullable();

            $table->json('puppies')->nullable();

            $table->string('status')->default('draft')->index();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breeding_inquiries');
    }
};
