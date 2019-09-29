<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\BaseTokenManager;
use Allegro\REST\Token\Token;
use Http\Mock\Client;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;

final class BaseTokenManagerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            BaseTokenManager::class,
            new BaseTokenManager(new Client)
        );
    }

    /**
     * @dataProvider errorStatusCodes
     */
    public function testRefreshTokenThrowsTransferExceptionOnErrorStatusCode($errorStatusCode): void
    {
        $client = new Client;
        $client->addResponse(new Response($errorStatusCode));

        $baseTokenManager = new BaseTokenManager($client);

        $this->expectException(TransferException::class);
        $baseTokenManager->refreshToken('clientId', 'clientSecret', 'redirectUri', new Token('accessToken', 'refreshToken'));
        
    }

    public function errorStatusCodes(): array
    {
        return [[400], [401], [402], [403], [404], [405], [500], [501], [502], [503], [504], [505]];
    }

    /**
     * @dataProvider invalidBodies
     */

    public function testRefreshTokenThrowsTransferExceptionOnInvalidBody($body): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $baseTokenManager = new BaseTokenManager($client);

        $this->expectException(TransferException::class);
        $baseTokenManager->refreshToken('clientId', 'clientSecret', 'redirectUri', new Token('accessToken', 'refreshToken'));
        
    }

    public function invalidBodies(): array
    {
        return [['invalid'], [''], ['{}'], ['{"access_token":"abc123"}'], [false], [null]];
    }

    /**
     * @dataProvider validResponses
     */
    public function testRefreshTokenRefreshesTokensOnValidResponse($body, $newAccessToken, $newRefreshToken): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $baseTokenManager = new BaseTokenManager($client);

        $token = new Token('accessToken', 'refreshToken');
        $baseTokenManager->refreshToken('clientId', 'clientSecret', 'redirectUri', $token);

        $this->assertEquals($newAccessToken, $token->getAccessToken());
        $this->assertEquals($newRefreshToken, $token->getRefreshToken());
    }

    public function validResponses(): array
    {
        return [
            ['{"access_token":"newAccessToken","refresh_token":"newRefreshToken"}', 'newAccessToken', 'newRefreshToken']
        ];
    }
}
