<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\Token;

class AuthorizationCodeTokenManager extends BaseTokenManager
{
    public function getUri(string $clientId, string $redirectUri): string
    {
        return self::AUTH_URI . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri
        ]);
    }

    public function getToken(string $clientId, string $clientSecret, string $redirectUri, string $code): Token
    {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getAuthorizationCodeTokenUri($redirectUri, $code),
            $this->getBasicAuthHeader($clientId, $clientSecret)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response);
        return $this->createTokenFromResponse($response);
    }

    protected function getAuthorizationCodeTokenUri(string $redirectUri, string $code)
    {
        return self::TOKEN_URI . '?' . http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri
        ]);
    }
}
