<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('fee_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('grade_level_id')->constrained('grade_levels')->onDelete('cascade');
            $table->decimal('tuition_fee', 10, 2)->default(0);

            // JSON column for other fees
            $table->json('other_fees')->nullable();

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fee_breakdowns');
    }
};
