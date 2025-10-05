<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dividends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->constrained();
            $table->date('declaration_date')->nullable();
            $table->date('ex_dividend_date')->nullable();
            $table->date('record_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 20, 5);
            $table->string('currency')->nullable();
            $table->string('source')->nullable();
            $table->string('source_reference')->nullable();
            $table->timestamps();

            $table->unique(['security_id', 'source_reference']);
            $table->index('security_id');
            $table->index('ex_dividend_date');
            $table->index('record_date');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dividends');
    }
};
