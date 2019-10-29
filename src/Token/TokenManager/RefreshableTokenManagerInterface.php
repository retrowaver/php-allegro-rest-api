<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\RefreshableTokenInterface;
use Http\Client\Exception\TransferException;
use Http\Client\Exception\HttpException;

interface RefreshableTokenManagerInterface
{
    /**
     * @throws TransferException on error
     * @throws HttpException on HTTP error status code
     * @param CredentialsInterface $credentials
     * @param RefreshableTokenInterface $token
     * @return RefreshableTokenInterface
     */
    public function refreshToken(
        CredentialsInterface $credentials,
        RefreshableTokenInterface $token
    ): RefreshableTokenInterface;
}
