<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained();
            $table->foreignId('security_id')->constrained();
            $table->decimal('current_shares_owned', 20, 5)->nullable();
            $table->decimal('current_average_price', 20, 5)->nullable();
            $table->decimal('current_cost_basis', 20, 5)->nullable();
            $table->timestamps();

            $table->index('portfolio_id');
            $table->index('security_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_metrics');
    }
};
