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
            // Drop the old foreign key if it exists
            $table->dropForeign(['request_id']);

            // Re-create the foreign key pointing to the correct table
            $table->foreign('request_id')
                  ->references('id')
                  ->on('request') // make sure this matches your actual table name
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_logs', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
        });
    }
};
