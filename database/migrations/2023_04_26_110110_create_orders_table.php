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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('order_details')->nullable();
            $table->string('payment_method')->nullable();
            $table->double('total_amount')->nullable();
            $table->double('discount')->nullable();
            $table->enum('order_status', ['processing', 'completed', 'canceled'])->default('processing'); // 'processing', 'completed', 'cancelled

            // paymob
            $table->string('paymob_transaction_id')->nullable();
            $table->string('paymob_order_id')->nullable();
            $table->double('paymob_amount_cents')->nullable();
            $table->string('paymob_pending')->nullable();
            $table->string('paymob_success')->nullable();

            $table->timestamps();

            $table->text('request')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
