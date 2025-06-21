<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Tests\Infrastructure\Persistence;

use BitCore\Modules\User\Models\User;
use BitCore\Modules\User\Repositories\Exceptions\UserNotFoundException;
use BitCore\Modules\User\Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'secret'
        ]);
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'secret'
        ]);

        $users = $this->userRepository->findAll();

        $this->assertCount(2, $users);
    }

    public function testFindUserOfId()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'secret'
        ]);

        $foundUser = $this->userRepository->findUserOfId($user->id);

        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testFindUserOfIdThrowsException()
    {
        $this->expectException(UserNotFoundException::class);
        $this->userRepository->findUserOfId(999);
    }
}
