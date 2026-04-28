<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\ListPlayerForTransferAction;
use App\Actions\RegisterUserAction;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(
        RegisterUserAction $register,
        ListPlayerForTransferAction $listForTransfer,
    ): void {
        $demoUsers = [
            ['name' => 'Test User', 'email' => 'test@example.com'],
            ['name' => 'Luka', 'email' => 'luka@example.com'],
            ['name' => 'Nika', 'email' => 'nika@example.com'],
            ['name' => 'Giorgi', 'email' => 'giorgi@example.com'],
            ['name' => 'Mariam', 'email' => 'mariam@example.com'],
        ];

        foreach ($demoUsers as $attributes) {
            $register->execute([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => Hash::make('password'),
            ]);
        }

        Team::query()->get()->each(function (Team $team) use ($listForTransfer): void {
            $players = Player::query()
                ->where('team_id', $team->id)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            foreach ($players as $player) {
                $listForTransfer->execute(
                    $player,
                    random_int(500_000, 3_000_000),
                );
            }
        });
    }
}
