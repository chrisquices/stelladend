<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('securities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('exchange_id')->nullable()->constrained();
            $table->string('ticker');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('market')->nullable();
            $table->string('color')->nullable();
            $table->tinyInteger('dividend_payout_frequency')->default(0);
            $table->boolean('has_logo')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('listed_at')->nullable();
            $table->date('first_traded_at')->nullable();
            $table->date('last_traded_at')->nullable();
            $table->timestamps();

            $table->index('uid');
            $table->index('ticker');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('securities');
    }
};
