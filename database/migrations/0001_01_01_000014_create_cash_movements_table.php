<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('portfolio_id')->constrained();
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->foreignId('dividend_received_id')->nullable()->constrained('dividends_received');
            $table->string('type'); // deposit, withdrawal, dividend, interest
            $table->decimal('amount', 20, 5);
            $table->string('description')->nullable();
            $table->string('note')->nullable();
            $table->date('date')->nullable();
            $table->boolean('is_imported')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
