<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->constrained();
            $table->decimal('open', 20, 5)->nullable();
            $table->decimal('high', 20, 5)->nullable();
            $table->decimal('low', 20, 5)->nullable();
            $table->decimal('close', 20, 5)->nullable();
            $table->decimal('volume', 20, 5)->nullable();
            $table->decimal('trades', 20, 5)->nullable();
            $table->decimal('change_in_dollar', 20, 5)->nullable();
            $table->decimal('change_in_percentage', 20, 5)->nullable();
            $table->string('source');
            $table->date('date');
            $table->timestamps();

            $table->unique(['security_id', 'date']);
            $table->index('security_id');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_quotes');
    }
};
