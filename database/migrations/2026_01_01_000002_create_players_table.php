<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('country');
            $table->string('position');
            $table->unsignedTinyInteger('age');
            $table->unsignedBigInteger('market_value')->default(1_000_000);
            $table->timestamps();

            $table->index('team_id');
            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
