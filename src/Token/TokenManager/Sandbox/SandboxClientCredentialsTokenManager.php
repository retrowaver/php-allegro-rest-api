<?php
namespace Allegro\REST\Token\TokenManager\Sandbox;

use Allegro\REST\Token\TokenManager\ClientCredentialsTokenManager;

class SandboxClientCredentialsTokenManager extends ClientCredentialsTokenManager
{
    const TOKEN_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/token';
    const AUTH_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/authorize';
}
