<?php
namespace Retrowaver\Allegro\REST\Token\TokenManager;

use Retrowaver\Allegro\REST\Token\AuthorizationCodeToken;
use Retrowaver\Allegro\REST\Token\AuthorizationCodeTokenInterface;
use Retrowaver\Allegro\REST\Token\CredentialsInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationCodeTokenManager extends RefreshableTokenManager implements AuthorizationCodeTokenManagerInterface
{
    public function getUri(CredentialsInterface $credentials): string
    {
        return static::AUTH_URI . '?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $credentials->getClientId(),
            'redirect_uri' => $credentials->getRedirectUri()
        ]);
    }

    public function getAuthorizationCodeToken(
        CredentialsInterface $credentials,
        string $code
    ): AuthorizationCodeTokenInterface {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getAuthorizationCodeTokenUri($credentials, $code),
            $this->getBasicAuthHeader($credentials)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response, ['access_token', 'refresh_token', 'expires_in']);
        return $this->createAuthorizationCodeTokenFromResponse($response);
    }

    protected function getAuthorizationCodeTokenUri(
        CredentialsInterface $credentials,
        string $code
    ): string {
        return static::TOKEN_URI . '?' . http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $credentials->getRedirectUri()
        ]);
    }

    protected function createAuthorizationCodeTokenFromResponse(
        ResponseInterface $response
    ): AuthorizationCodeTokenInterface {
        $decoded = json_decode((string)$response->getBody());
        return new AuthorizationCodeToken(
            $decoded->access_token,
            $decoded->refresh_token,
            $decoded->expires_in
        );
    }
}
