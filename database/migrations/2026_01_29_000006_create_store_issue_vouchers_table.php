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
        Schema::create('store_issue_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(now());
            $table->string('request_no')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('receiver_name')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('plate_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_issue_vouchers');
    }
};
