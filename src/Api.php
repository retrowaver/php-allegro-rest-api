<?php
namespace Allegro\REST;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\MessageFactory;
use Http\Discovery\MessageFactoryDiscovery;
use Allegro\REST\Token\TokenInterface;
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
    const CUSTOM_HEADERS = [
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
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var array
     */
    protected $customHeaders;

    /**
     * @param HttpClient|null $client
     * @param MessageFactory|null $messageFactory
     * @param TokenInterface $token
     * @param MiddlewareInterface[] $middleware
     */
    public function __construct(
        ?HttpClient $client = null,
        ?MessageFactory $messageFactory = null,
        array $middleware = []
    ) {
        $this->client = new HttplugMiddlewareDecorator(
            $client ?? HttpClientDiscovery::find(),
            $middleware
        );
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();

        $this->setCustomHeaders(self::CUSTOM_HEADERS);
    }

    /**
     * @param TokenInterface $token
     * @return self
     */
    public function setToken(TokenInterface $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return TokenInterface
     */
    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * @param null|array $queryParams
     * @return string
     */
    public function getUri(?array $queryParams = null): string
    {
        return static::API_URI;
    }

    /**
     * @return array
     */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * @param array $customHeaders
     * @return self
     */
    public function setCustomHeaders(array $customHeaders): self
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    /**
     * @param array $customHeaders
     * @return self
     */
    public function addCustomHeaders(array $customHeaders): self
    {
        $this->customHeaders += $customHeaders;
        return $this;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token->getAccessToken()] + $this->customHeaders;
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
