<?php
namespace Allegro\REST\Token\TokenManager;

use Allegro\REST\Token\CredentialsInterface;
use Allegro\REST\Token\ClientCredentialsTokenInterface;

interface ClientCredentialsTokenManagerInterface
{
    public function getClientCredentialsToken(
        CredentialsInterface $credentials
    ): ClientCredentialsTokenInterface;
}
