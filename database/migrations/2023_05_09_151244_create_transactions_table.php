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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('type', ['credit', 'debit']);
            $table->float('amount');
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('restrict');
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
