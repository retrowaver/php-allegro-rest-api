<?php
namespace Retrowaver\Allegro\REST\Token\TokenManager\Sandbox;

use Retrowaver\Allegro\REST\Token\TokenManager\ClientCredentialsTokenManager;

class SandboxClientCredentialsTokenManager extends ClientCredentialsTokenManager
{
    const TOKEN_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/token';
    const AUTH_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/authorize';
}
