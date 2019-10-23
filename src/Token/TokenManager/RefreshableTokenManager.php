<?php
namespace Allegro\REST\Token\TokenManager;

use Psr\Http\Message\ResponseInterface;
use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\RefreshableTokenInterface;

class RefreshableTokenManager extends BaseTokenManager implements RefreshableTokenManagerInterface
{
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
