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
       Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');  // FontAwesome icon class
            $table->text('description')->nullable();
            $table->string('url')->nullable();  // Optional route or URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
