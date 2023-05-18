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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image')->nullable( );
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->enum('type', ['user', 'admin'])->default('user');
            $table->enum('network_type', ['basic', 'advanced'])->default('basic');
            
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users')->onDelete('restrict');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
