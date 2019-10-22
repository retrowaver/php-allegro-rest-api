<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\AuthorizationCodeToken;
use Allegro\REST\Token\AuthorizationCodeTokenInterface;
use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\RefreshableTokenInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizationCodeTokenManager extends BaseTokenManager implements AuthorizationCodeTokenManagerInterface
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

        $this->validateGetTokenResponse($request, $response, ['access_token', 'refresh_token']);
        return $this->createAuthorizationCodeTokenFromResponse($response);
    }

    public function refreshToken(
        CredentialsInterface $credentials,
        RefreshableTokenInterface $token
    ): RefreshableTokenInterface {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getRefreshTokenUri($credentials, $token),
            $this->getBasicAuthHeader($credentials)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response, ['access_token', 'refresh_token']);
        return $this->updateRefreshedTokenFromResponse($token, $response);
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

    protected function getRefreshTokenUri(
        CredentialsInterface $credentials,
        RefreshableTokenInterface $token
    ) {
        return static::TOKEN_URI . "?" . http_build_query([
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->getRefreshToken(),
            'redirect_uri' => $credentials->getRedirectUri()
        ]);
    }

    protected function createAuthorizationCodeTokenFromResponse(
        ResponseInterface $response
    ): AuthorizationCodeTokenInterface {
        $decoded = json_decode((string)$response->getBody());
        return new AuthorizationCodeToken($decoded->access_token, $decoded->refresh_token);
    }

    protected function updateRefreshedTokenFromResponse(
        RefreshableTokenInterface $token,
        ResponseInterface $response
    ): RefreshableTokenInterface {
        $decoded = json_decode((string)$response->getBody());

        return $token
            ->setAccessToken($decoded->access_token)
            ->setRefreshToken($decoded->refresh_token)
        ;
    }
}
