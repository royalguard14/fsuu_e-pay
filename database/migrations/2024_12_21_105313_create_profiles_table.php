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
        Schema::create('profiles', function (Blueprint $table) {
        $table->id();
        $table->string('lrn')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('firstname');
        $table->string('lastname');
        $table->string('phone_number')->nullable();  
        $table->string('address')->nullable();  
        $table->string('profile_picture')->nullable();  
        $table->date('birthdate')->nullable();  
        $table->string('gender')->nullable();  
        $table->string('nationality')->nullable();  
        $table->text('bio')->nullable();  
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
