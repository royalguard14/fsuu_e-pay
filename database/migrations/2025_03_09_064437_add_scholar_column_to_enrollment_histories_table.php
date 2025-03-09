<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('enrollment_histories', function (Blueprint $table) {
            $table->boolean('scholar')->default(0)->after('enrollment_date'); // Add the column after 'enrollment_date'
        });
    }

    public function down() {
        Schema::table('enrollment_histories', function (Blueprint $table) {
            $table->dropColumn('scholar');
        });
    }
};
