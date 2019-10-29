<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\AuthorizationCodeTokenInterface;
use Http\Client\Exception\TransferException;
use Http\Client\Exception\HttpException;

interface AuthorizationCodeTokenManagerInterface extends RefreshableTokenManagerInterface
{
    /**
     * @param CredentialsInterface $credentials
     * @return string
     */
    public function getUri(CredentialsInterface $credentials): string;

    /**
     * @throws TransferException on error
     * @throws HttpException on HTTP error status code
     * @param CredentialsInterface $credentials
     * @param string $code
     * @return AuthorizationCodeTokenInterface
     */
    public function getAuthorizationCodeToken(
        CredentialsInterface $credentials,
        string $code
    ): AuthorizationCodeTokenInterface;
}
