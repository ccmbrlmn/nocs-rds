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
    Schema::table('user_logs', function (Blueprint $table) {
        if (!Schema::hasColumn('user_logs', 'request_id')) { 
            $table->unsignedBigInteger('request_id')->nullable();

            // Set the foreign key properly to the singular table 'request'
            $table->foreign('request_id')
                  ->references('id')
                  ->on('request')
                  ->cascadeOnDelete();
        }
    });
}

public function down(): void
{
    Schema::table('user_logs', function (Blueprint $table) {
        if (Schema::hasColumn('user_logs', 'request_id')) {
            $table->dropForeign(['request_id']);
            $table->dropColumn('request_id');
        }
    });
}
};
