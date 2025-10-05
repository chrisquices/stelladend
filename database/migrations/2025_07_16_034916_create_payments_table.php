<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('btc_address_id')->constrained();
            $table->string('plan')->nullable(); // 'yearly' or 'lifetime'
            $table->decimal('btc_price', 20, 5)->nullable();
            $table->decimal('amount_usd', 20, 5)->nullable();
            $table->decimal('amount_btc', 20, 8)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
