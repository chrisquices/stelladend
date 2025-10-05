<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('portfolio_id')->constrained();
            $table->foreignId('security_id')->constrained();
            $table->tinyInteger('batch')->default(1)->nullable();
            $table->tinyInteger('order')->default(1)->nullable();
            $table->tinyInteger('true_order')->default(1)->nullable();
            $table->string('type');
            $table->decimal('current_shares_at_this_point_in_time', 20, 5)->nullable();
            $table->decimal('shares', 20, 5);
            $table->decimal('price', 20, 5);
            $table->decimal('fees_and_commissions', 20, 5);
            $table->decimal('total', 20, 5);
            $table->decimal('trade_value', 20, 5);
            $table->decimal('gain_loss_in_dollar', 20, 5)->nullable();
            $table->decimal('gain_loss_in_percentage', 20, 2)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_imported')->default(false);
            $table->boolean('has_exited')->default(false);
            $table->boolean('has_entered')->default(false);
            $table->date('trade_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
