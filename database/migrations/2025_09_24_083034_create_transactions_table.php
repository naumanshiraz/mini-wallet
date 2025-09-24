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
          $table->timestamps();

          $table->unsignedBigInteger('sender_id')->index();
          $table->unsignedBigInteger('receiver_id')->index();
          $table->decimal('amount', 18, 2);
          $table->decimal('commission_fee', 18, 2);
          $table->decimal('sender_balance_after', 18, 2);
          $table->decimal('receiver_balance_after', 18, 2);
          $table->text('meta')->nullable();

          $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
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
