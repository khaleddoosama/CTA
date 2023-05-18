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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->double('selling_price')->nullable();
            $table->double('discount_price')->nullable();
            $table->double('quantity')->nullable();
            $table->string('image')->nullable();
            $table->integer('status')->nullable();

            // category_id must be nullable
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
