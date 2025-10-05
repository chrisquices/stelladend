<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dividends_received', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained();
            $table->foreignId('security_id')->constrained();
            $table->foreignId('dividend_id')->constrained();
            $table->decimal('shares', 20, 5);
            $table->decimal('dividend_amount', 20, 5);
            $table->decimal('total', 20, 5);
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dividends_received');
    }
};
