<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Allegro\REST\Token\Token;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;

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
        $clientCredentialsTokenManager->getToken('clientId', 'clientSecret');
    }

    public function testGetTokenReturnsTokenOnValidCredentials(): void
    {
        $client = new Client;

        $clientCredentialsTokenManager = new SandboxClientCredentialsTokenManager($client);

        $credentials = require(__DIR__ . '/../config.php');

        $token = $clientCredentialsTokenManager->getToken(
            $credentials['clientId'],
            $credentials['clientSecret']
        );

        $this->assertInstanceOf(
            Token::class,
            $token
        );
    }
}
