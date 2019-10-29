<?php
use Http\Adapter\Guzzle6\Client;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Allegro\REST\Token\Credentials;

$config = require(__DIR__ . '/config.php');

$client = new Client;
$clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);
return $clientCredentialsTokenManager->getClientCredentialsToken(
    $config['credentials']
);
