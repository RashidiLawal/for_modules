<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Test\Domain;

use BitCore\Modules\User\Models\User;
use BitCore\Modules\User\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class UserTest extends TestCase
{
    public static function userProvider(): array
    {
        return [
            [1, 'bill@gates.com', 'Bill', 'Gates', 'password'],
            [2, 'steve@jobs.com', 'Steve', 'Jobs', 'password'],
        ];
    }

    #[DataProvider('userProvider')]
    public function testJsonSerialize(int $id, string $email, string $firstName, string $lastName)
    {
        $user = new User(['email' => $email, 'first_name' => $firstName, 'last_name' => $lastName]);
        $user->id = $id;

        $expectedPayload = json_encode([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'id' => $id,
        ]);

        $this->assertEquals($expectedPayload, json_encode($user));
    }
}
