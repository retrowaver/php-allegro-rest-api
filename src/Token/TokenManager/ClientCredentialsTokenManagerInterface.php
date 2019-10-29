<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\ClientCredentialsTokenInterface;
use Http\Client\Exception\TransferException;
use Http\Client\Exception\HttpException;

interface ClientCredentialsTokenManagerInterface
{
    /**
     * @throws TransferException on error
     * @throws HttpException on HTTP error status code
     * @param CredentialsInterface $credentials
     * @return ClientCredentialsTokenInterface
     */
    public function getClientCredentialsToken(
        CredentialsInterface $credentials
    ): ClientCredentialsTokenInterface;
}
