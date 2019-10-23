<?php
namespace Allegro\REST\Token\TokenManager;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\MessageFactory;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Exception\TransferException;
use Http\Client\Exception\HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Allegro\REST\Token\CredentialsInterface;

abstract class BaseTokenManager
{
    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';
    const AUTH_URI = 'https://allegro.pl/auth/oauth/authorize';

    protected $client;
    protected $messageFactory;

    public function __construct(
        ?HttpClient $client = null,
        ?MessageFactory $messageFactory = null
    ) {
        $this->client = $client ?? HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
    }

    protected function getBasicAuthHeader(CredentialsInterface $credentials): array
    {
        return [
            'Authorization' => "Basic " . base64_encode($credentials->getClientId() . ':' . $credentials->getClientSecret())
        ];
    }

    protected function validateGetTokenResponse(
        RequestInterface $request,
        ResponseInterface $response,
        array $requiredFields = []
    ) {
        $decoded = json_decode((string)$response->getBody());

        if (isset($decoded->error)) {
            throw new TransferException((string)$decoded->error);
        }

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() <= 599) {
            throw HttpException::create($request, $response);
        }

        foreach ($requiredFields as $field) {
            if (!isset($decoded->{$field})) {
                throw new TransferException("Couldn't extract data from response.");
            }
        }
    }
}
