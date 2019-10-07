<?php
namespace Allegro\REST\Token;

class Token
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string|null
     */
    protected $refreshToken;

    public function __construct(string $accessToken, ?string $refreshToken = null)
    {
        $this->setAccessToken($accessToken);
        $this->setRefreshToken($refreshToken);
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $accessToken
     * @return Token
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @param string|null $refreshToken
     * @return Token
     */
    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }
}