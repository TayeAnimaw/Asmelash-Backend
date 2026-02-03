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
        Schema::create('store_issue_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_issue_voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_code')->nullable();
            $table->string('description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_issue_voucher_items');
    }
};
