<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\Token;
use Psr\Http\Message\ResponseInterface;

class ClientCredentialsTokenManager extends BaseTokenManager
{
    public function getToken(string $clientId, string $clientSecret): Token
    {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getClientCredentialsTokenUri(),
            $this->getBasicAuthHeader($clientId, $clientSecret)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response);
        return $this->createTokenFromResponse($response);
    }

    protected function getClientCredentialsTokenUri(): string
    {
        return static::TOKEN_URI . "?" . http_build_query([
            'grant_type' => 'client_credentials'
        ]);
    }
}
