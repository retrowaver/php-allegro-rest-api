<?php
use Http\Adapter\Guzzle6\Client;
use Retrowaver\Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Retrowaver\Allegro\REST\Token\Credentials;

$config = require(__DIR__ . '/config.php');

$client = new Client;
$clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);
return $clientCredentialsTokenManager->getClientCredentialsToken(
    $config['credentials']
);
