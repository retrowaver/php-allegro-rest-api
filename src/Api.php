<?php
namespace Allegro\REST;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\MessageFactory;
use Http\Discovery\MessageFactoryDiscovery;
use Allegro\REST\Token\Token;
use Allegro\REST\Middleware\HttplugMiddlewareDecorator;
use Allegro\REST\Middleware\MiddlewareInterface;

class Api extends Resource
{
    /**
     * @var string
     */
    const API_URI = 'https://api.allegro.pl';

    /**
     * @var string
     */
    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';

    /**
     * @var array
     */
    const DEFAULT_HEADERS = [
        'Content-Type' => 'application/vnd.allegro.public.v1+json',
        'Accept' => 'application/vnd.allegro.public.v1+json'
    ];

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @param HttpClient|null $client
     * @param MessageFactory|null $messageFactory
     * @param Token $token
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        ?HttpClient $client = null,
        ?MessageFactory $messageFactory = null,
        Token $token,
        array $middleware = []
    ) {
        $this->client = new HttplugMiddlewareDecorator(
            $client ?? HttpClientDiscovery::find(),
            $middleware
        );
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
        $this->token = $token;
        $this->setHeaders(self::DEFAULT_HEADERS);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return static::API_URI;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token->getAccessToken()] + $this->headers;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return HttpClient
     */
    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    /**
     * @return MessageFactory
     */
    protected function getMessageFactory(): MessageFactory
    {
        return $this->messageFactory;
    }
}
