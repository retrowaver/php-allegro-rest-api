<?php
namespace Allegro\REST\Token;

trait RefreshableTokenTrait
{
    /**
     * @var string
     */
    protected $refreshToken;
    
    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return RefreshableTokenInterface
     */
    public function setRefreshToken(string $refreshToken): RefreshableTokenInterface
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }
}
