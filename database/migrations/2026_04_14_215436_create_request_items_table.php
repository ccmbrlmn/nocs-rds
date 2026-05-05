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
            Schema::create('request_items', function (Blueprint $table) {
        $table->id();

        // Link to requests table
        $table->foreignId('request_id')->constrained()->onDelete('cascade');

        // Item details (from user input)
        $table->string('item_name');
        $table->integer('quantity');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_items');
    }
};
