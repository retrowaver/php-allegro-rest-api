<?php
use Allegro\REST\Token\Token;
use Http\Adapter\Guzzle6\Client;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;

$credentials = require(__DIR__ . '/config.php');

$client = new Client;
$clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);
return $clientCredentialsTokenManager->getToken(
    $credentials['clientId'],
    $credentials['clientSecret']
);
