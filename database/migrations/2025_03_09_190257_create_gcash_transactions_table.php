<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('gcash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student making the payment
            $table->foreignId('gcash_information_id')->constrained('gcash_information')->onDelete('cascade'); // GCash account info
            $table->decimal('amount', 10, 2); // Payment amount
            $table->string('reference_number')->nullable(); // Optional, in case they provide it
            $table->string('receipt')->nullable(); // File path of the uploaded receipt
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('gcash_transactions');
    }
};
