<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Test\Actions;

use BitCore\Application\Actions\ActionPayload;
use BitCore\Modules\User\Repositories\UserRepository;
use BitCore\Modules\User\Models\User;
use BitCore\Modules\User\Tests\TestCase;

class ListUserActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container */
        $container = $app->getContainer();

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'secret'
        ]);
        $user = $container->get(UserRepository::class)->findUserOfId($user->id);

        $request = $this->createRequest('GET', '/users');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$user]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
