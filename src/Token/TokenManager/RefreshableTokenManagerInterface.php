<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\RefreshableTokenInterface;

interface RefreshableTokenManagerInterface
{
    public function refreshToken(
        CredentialsInterface $credentials,
        RefreshableTokenInterface $token
    ): RefreshableTokenInterface;
}
