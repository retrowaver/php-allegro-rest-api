<?php
namespace Allegro\REST\Token;

interface RefreshableTokenInterface extends TokenInterface
{
    /**
     * @return string
     */
    public function getRefreshToken(): string;

    /**
     * @param string $refreshToken
     * @return RefreshableTokenInterface
     */
    public function setRefreshToken(string $refreshToken): RefreshableTokenInterface;
}
