<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class TeamController extends Controller
{
    public function __construct(
        private readonly TeamService $teams,
    ) {}

    public function show(): JsonResponse
    {
        $team = $this->teams->getForUser($this->user());

        return response()->json([
            'data' => new TeamResource($team),
        ]);
    }

    public function update(UpdateTeamRequest $request): JsonResponse
    {
        $team = $this->teams->getForUser($this->user());
        $this->authorize('update', $team);

        $updated = $this->teams->update($team, $request->payload());

        return response()->json([
            'message' => __('soccer.team.updated'),
            'data' => new TeamResource($updated),
        ]);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
