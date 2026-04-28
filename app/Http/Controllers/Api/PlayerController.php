<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\User;
use App\Services\PlayerService;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class PlayerController extends Controller
{
    public function __construct(
        private readonly PlayerService $players,
        private readonly TeamService $teams,
    ) {}

    public function index(): JsonResponse
    {
        $team = $this->teams->getForUser($this->user());
        $players = $this->players->listForTeam($team);

        return response()->json([
            'data' => PlayerResource::collection($players),
        ]);
    }

    public function update(UpdatePlayerRequest $request, int $playerId): JsonResponse
    {
        $player = $this->players->find($playerId);
        $this->authorize('update', $player);

        $updated = $this->players->update($player, $request->payload());

        return response()->json([
            'message' => __('soccer.player.updated'),
            'data' => new PlayerResource($updated),
        ]);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
