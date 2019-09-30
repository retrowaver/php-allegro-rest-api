<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\Token;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\MessageFactory;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Exception\TransferException;
use Http\Client\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class BaseTokenManager
{
    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';

    protected $client;
    protected $messageFactory;

    public function __construct(?HttpClient $client = null, ?MessageFactory $messageFactory = null)
    {
        $this->client = $client ?? HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
    }

    public function refreshToken(string $clientId, string $clientSecret, string $redirectUri, Token $token): void
    {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getRefreshTokenUri($redirectUri, $token),
            $this->getBasicAuthHeader($clientId, $clientSecret)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response);
        $this->updateTokenWithValidResponse($token, $response);
    }

    protected function getBasicAuthHeader(string $clientId, string $clientSecret): array
    {
        return [
            'Authorization' => "Basic " . base64_encode($clientId . ':' . $clientSecret)
        ];
    }

    protected function getRefreshTokenUri(string $redirectUri, Token $token): string
    {
        return self::TOKEN_URI . "?" . http_build_query([
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->getRefreshToken(),
            'redirect_uri' => $redirectUri
        ]);
    }

    protected function validateGetTokenResponse(RequestInterface $request, ResponseInterface $response)
    {
        $decoded = json_decode((string)$response->getBody());

        if (isset($decoded->error)) {
            throw new TransferException((string)$decoded->error);
        }

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() <= 599) {
            throw HttpException::create($request, $response);
        }

        if (!isset($decoded->access_token) || !isset($decoded->refresh_token)) {
            throw new TransferException("Couldn't get tokens from response.");
        }
    }

    protected function updateTokenWithValidResponse(Token $token, ResponseInterface $response)
    {
        $decoded = json_decode((string)$response->getBody());
        $token->setAccessToken($decoded->access_token);
        $token->setRefreshToken($decoded->refresh_token);
    }
}
