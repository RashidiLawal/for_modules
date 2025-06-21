<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Test\Actions;

use BitCore\Application\Actions\ActionError;
use BitCore\Application\Actions\ActionPayload;
use BitCore\Modules\User\Models\User;
use BitCore\Modules\User\Repositories\UserRepository;
use BitCore\Modules\User\Tests\TestCase;

class ViewUserActionTest extends TestCase
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

        $request = $this->createRequest('GET', '/users/' . $user->id);
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, $user);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    // public function testActionThrowsUserNotFoundException()
    // {
    //     $app = $this->getAppInstance();

    //     $request = $this->createRequest('GET', '/users/1');
    //     $response = $app->handle($request);

    //     $payload = (string) $response->getBody();
    //     $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
    //     $expectedPayload = new ActionPayload(404, null, $expectedError);
    //     $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

    //     $this->assertEquals($serializedPayload, $payload);
    // }
}
