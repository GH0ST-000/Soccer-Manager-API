<?php

declare(strict_types=1);

use App\Enums\TransferListingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('seller_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedBigInteger('asking_price');
            $table->string('status')->default(TransferListingStatus::Active->value);
            $table->timestamps();

            $table->index('status');
            $table->index('seller_team_id');
            $table->index('player_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_listings');
    }
};
