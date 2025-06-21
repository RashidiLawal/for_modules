<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Repositories;

use BitCore\Modules\User\Models\User;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws Exceptions\UserNotFoundException
     */
    public function findUserOfId(int $id): User;
}
