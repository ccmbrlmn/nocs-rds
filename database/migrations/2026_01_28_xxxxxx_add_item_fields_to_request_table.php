<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request', function (Blueprint $table) {
            $table->string('item_name')->nullable()->after('purpose');
            $table->string('item_number')->nullable()->after('item_name');
        });
    }

    public function down(): void
    {
        Schema::table('request', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'item_number']);
        });
    }
};
