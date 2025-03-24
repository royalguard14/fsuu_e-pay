<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('gcash_information', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_number');
            $table->text('qrcode')->nullable();
            $table->boolean('isActive')->default(false); 
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('gcash_information');
    }
};
