<?php

namespace BitCore\Application\Actions;

use BitCore\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class CsrfCookieAction
 *
 * This action handles the generation and retrieval of CSRF tokens.
 * It provides a CSRF token pair that can be used for securing client requests.
 */
class CsrfCookieAction extends Action
{
    /**
     * Generates and returns a CSRF token pair.
     *
     * @return Response JSON response containing the CSRF token name and value.
     */
    public function action(): Response
    {
        // Retrieve the CSRF guard instance from the container
        $csrf = $this->container->get('csrf');

        // Get CSRF token keys
        $csrfKeyName = $csrf->getTokenNameKey();
        $csrfKeyValue = $csrf->getTokenValueKey();

        // Generate a new CSRF token pair
        $csrfPair = $csrf->generateToken();

        // Store the CSRF tokens in a cookie
        $response = $this->response->withHeader('Set-Cookie', sprintf(
            "%s=%s; Path=/; Secure; SameSite=Lax",
            $csrfPair[$csrfKeyName],
            $csrfPair[$csrfKeyValue]
        ));

        // Return the generated CSRF tokens in a JSON response
        return $this->respondWithData([
            'csrf_name' => $csrfPair[$csrfKeyName],
            'csrf_value' => $csrfPair[$csrfKeyValue],
            'csrf_name_key' => $csrfKeyName,
            'csrf_value_key' => $csrfKeyValue,
        ], 200)->withHeader('Set-Cookie', $response->getHeaderLine('Set-Cookie'));
    }
}
