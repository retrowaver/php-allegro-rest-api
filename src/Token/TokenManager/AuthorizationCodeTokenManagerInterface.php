<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\AuthorizationCodeTokenInterface;

interface AuthorizationCodeTokenManagerInterface extends RefreshableTokenManagerInterface
{
    /**
     * @param CredentialsInterface $credentials
     * @return string
     */
    public function getUri(CredentialsInterface $credentials): string;

    /**
     * @param CredentialsInterface $credentials
     * @param string $code
     * @return AuthorizationCodeTokenInterface
     */
    public function getAuthorizationCodeToken(
        CredentialsInterface $credentials,
        string $code
    ): AuthorizationCodeTokenInterface;
}
