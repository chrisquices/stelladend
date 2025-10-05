<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained();
            $table->decimal('current_cash_available', 20, 5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_metrics');
    }
};
