<?php
namespace Allegro\REST\Token;

trait TokenTrait
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var int
     */
    protected $expiresIn;
     
    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return TokenInterface
     */
    public function setAccessToken(string $accessToken): TokenInterface
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     * @return TokenInterface
     */
    public function setExpiresIn(int $expiresIn): TokenInterface
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }
}
