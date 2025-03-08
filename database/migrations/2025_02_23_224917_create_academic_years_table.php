<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->year('start');
            $table->year('end');
            $table->boolean('current')->default(false); // New field for active year
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('academic_years');
    }
};
