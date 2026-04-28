<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\ListPlayerRequest;
use App\Http\Requests\Transfer\SearchTransferListingRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\TransferListingResource;
use App\Models\User;
use App\Services\PlayerService;
use App\Services\TeamService;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transfers,
        private readonly PlayerService $players,
        private readonly TeamService $teams,
    ) {}

    public function index(SearchTransferListingRequest $request): AnonymousResourceCollection
    {
        $listings = $this->transfers->search($request->filters(), $request->perPage());

        return TransferListingResource::collection($listings);
    }

    public function listPlayer(ListPlayerRequest $request, int $playerId): JsonResponse
    {
        $player = $this->players->find($playerId);
        $this->authorize('listForTransfer', $player);

        $listing = $this->transfers->listPlayer($player, $request->askingPrice());

        return response()->json([
            'message' => __('soccer.transfer.listed'),
            'data' => new TransferListingResource($listing),
        ], Response::HTTP_CREATED);
    }

    public function cancelListing(int $playerId): JsonResponse
    {
        $player = $this->players->find($playerId);
        $this->authorize('cancelTransfer', $player);

        $listing = $this->transfers->cancelListing($player);

        return response()->json([
            'message' => __('soccer.transfer.cancelled'),
            'data' => new TransferListingResource($listing),
        ]);
    }

    public function buy(int $listingId): JsonResponse
    {
        $team = $this->teams->getForUser($this->user());
        $transaction = $this->transfers->buy($listingId, $team);

        return response()->json([
            'message' => __('soccer.transfer.bought'),
            'data' => new TransactionResource($transaction),
        ], Response::HTTP_CREATED);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
