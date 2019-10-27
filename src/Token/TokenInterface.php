<?php
namespace Allegro\REST\Token;

interface TokenInterface
{
    /**
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * @param string $accessToken
     * @return TokenInterface
     */
    public function setAccessToken(string $accessToken): TokenInterface;

    /**
     * @return int
     */
    public function getExpiresIn(): int;

    /**
     * @param int $expiresIn
     * @return TokenInterface
     */
    public function setExpiresIn(int $expiresIn): TokenInterface;
}
