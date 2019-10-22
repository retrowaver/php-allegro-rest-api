<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxAuthorizationCodeTokenManager;
use Allegro\REST\Token\AuthorizationCodeToken;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Allegro\REST\Token\Credentials;

final class SandboxAuthorizationCodeTokenManagerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            SandboxAuthorizationCodeTokenManager::class,
            new SandboxAuthorizationCodeTokenManager(new Client)
        );
    }

    public function testRefreshTokenThrowsTransferExceptionOnInvalidCredentials(): void
    {
        $client = new Client;

        $authorizationCodeTokenManager = new SandboxAuthorizationCodeTokenManager($client);

        $this->expectException(TransferException::class);
        $authorizationCodeTokenManager->refreshToken(
            new Credentials([]),
            new AuthorizationCodeToken('accessToken', 'refreshToken')
        );
    }
}
