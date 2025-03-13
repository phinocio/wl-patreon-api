<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patreon_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('access');
            $table->string('refresh');
            $table->string('client_secret');
            $table->string('expires_in');
            $table->timestamp('expires');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patreon_tokens');
    }
};
