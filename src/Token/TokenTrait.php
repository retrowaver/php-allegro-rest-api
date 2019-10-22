<?php
namespace Allegro\REST\Token;

trait TokenTrait
{
    /**
     * @var string
     */
    protected $accessToken;
    
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
}
