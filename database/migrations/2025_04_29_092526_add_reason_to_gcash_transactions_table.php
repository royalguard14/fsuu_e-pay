<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('gcash_transactions', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status');
        });
    }

    public function down() {
        Schema::table('gcash_transactions', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
