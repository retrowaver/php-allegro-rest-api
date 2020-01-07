<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Retrowaver\Allegro\REST\Token\ClientCredentialsToken;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Retrowaver\Allegro\REST\Token\Credentials;

final class SandboxClientCredentialsTokenManagerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            SandboxClientCredentialsTokenManager::class,
            new SandboxClientCredentialsTokenManager(new Client)
        );
    }

    public function testGetTokenThrowsTransferExceptionOnInvalidCredentials(): void
    {
        $client = new Client;

        $clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);

        $this->expectException(TransferException::class);
        $clientCredentialsTokenManager->getClientCredentialsToken(
            new Credentials([])
        );
    }

    public function testGetTokenReturnsTokenOnValidCredentials(): void
    {
        $client = new Client;
        $clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);
        $config = require(__DIR__ . '/../config.php');

        $token = $clientCredentialsTokenManager->getClientCredentialsToken(
            $config['credentials']
        );

        $this->assertInstanceOf(
            ClientCredentialsToken::class,
            $token
        );
    }
}
