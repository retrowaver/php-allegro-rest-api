<?php
namespace Allegro\REST\Token\TokenManager\Sandbox;

use Allegro\REST\Token\TokenManager\AuthorizationCodeTokenManager;

class SandboxAuthorizationCodeTokenManager extends AuthorizationCodeTokenManager
{
    const TOKEN_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/token';
    const AUTH_URI = 'https://allegro.pl.allegrosandbox.pl/auth/oauth/authorize';
}
