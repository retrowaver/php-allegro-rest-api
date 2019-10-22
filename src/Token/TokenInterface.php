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
}
