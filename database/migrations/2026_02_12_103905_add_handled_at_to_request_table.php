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
            Schema::table('request', function (Blueprint $table) {
        $table->timestamp('handled_at')->nullable()->after('handled_by');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('request', function (Blueprint $table) {
        $table->dropColumn('handled_at');
    });
    }
};
