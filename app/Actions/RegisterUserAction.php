<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

final readonly class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $users,
        private CreateInitialTeamAction $createInitialTeam,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string}  $attributes
     */
    public function execute(array $attributes): User
    {
        /** @var User $user */
        $user = DB::transaction(function () use ($attributes): User {
            $user = $this->users->create($attributes);
            $this->createInitialTeam->execute($user);

            return $user->refresh();
        });

        return $user;
    }
}
