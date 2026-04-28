<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('seller_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('buyer_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('old_value');
            $table->unsignedBigInteger('new_value');
            $table->timestamps();

            $table->index('player_id');
            $table->index('buyer_team_id');
            $table->index('seller_team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
