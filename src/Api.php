<?php
namespace Allegro\REST;

class Api extends Resource
{

    const API_URI = 'https://api.allegro.pl';

    const TOKEN_URI = 'https://allegro.pl/auth/oauth/token';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var Token
     */
    protected $token;

    /**
     * Api constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param Token $token
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $token)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return static::API_URI;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
