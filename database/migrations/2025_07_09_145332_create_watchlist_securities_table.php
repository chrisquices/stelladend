<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlist_securities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watchlist_id')->constrained();
            $table->foreignId('security_id')->constrained();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_securities');
    }
};
