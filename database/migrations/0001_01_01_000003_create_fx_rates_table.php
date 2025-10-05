<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fx_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->constrained('currencies');
            $table->foreignId('to_currency_id')->constrained('currencies');
            $table->decimal('rate', 20, 5);
            $table->date('date');
            $table->timestamps();

            $table->unique(['from_currency_id', 'to_currency_id', 'date']);
            $table->index('from_currency_id');
            $table->index('to_currency_id');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fx_rates');
    }
};
