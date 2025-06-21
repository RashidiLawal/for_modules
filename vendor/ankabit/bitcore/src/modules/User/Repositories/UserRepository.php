<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Repositories;

use BitCore\Modules\User\Models\User;
use BitCore\Modules\User\Repositories\Exceptions\UserNotFoundException;
use BitCore\Modules\User\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find and return all users.
     *
     * @return User[] An array of User objects.
     */
    public function findAll(): array
    {
        return User::all()->toArray();
    }

    /**
     * Find and return a user by their ID.
     *
     * @param int $id The ID of the user to find.
     * @return User The User object with the specified ID.
     * @throws UserNotFoundException If the user with the specified ID is not found.
     */
    public function findUserOfId(int $id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
