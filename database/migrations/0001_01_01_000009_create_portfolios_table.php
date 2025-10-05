<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->string('mode'); // simple // advanced
            $table->string('public_reference_code')->unique();
            $table->boolean('is_default');
            $table->boolean('is_public');
            $table->boolean('is_import_only');
            $table->boolean('is_included_in_calendar');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
