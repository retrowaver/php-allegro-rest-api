<?php
namespace Allegro\REST\Token;

class Credentials implements CredentialsInterface
{
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
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {
        $this
            ->setClientId($credentials['clientId'] ?? '')
            ->setClientSecret($credentials['clientSecret'] ?? '')
            ->setRedirectUri($credentials['redirectUri'] ?? '')
        ;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $clientId
     * @return CredentialsInterface
     */
    public function setClientId(string $clientId): CredentialsInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param string $clientSecret
     * @return CredentialsInterface
     */
    public function setClientSecret(string $clientSecret): CredentialsInterface
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @param string $redirectUri
     * @return CredentialsInterface
     */
    public function setRedirectUri(string $redirectUri): CredentialsInterface
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }
}
