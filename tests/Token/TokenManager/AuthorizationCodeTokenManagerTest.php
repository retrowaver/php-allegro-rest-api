<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\AuthorizationCodeTokenManager;
use Http\Mock\Client;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;
use Allegro\REST\Token\Token;

final class AuthorizationCodeTokenManagerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            AuthorizationCodeTokenManager::class,
            new AuthorizationCodeTokenManager(new Client)
        );
    }

    public function testGetUriGetsValidUri(): void
    {
        $tokenManager = new AuthorizationCodeTokenManager(new Client);

        $this->assertEquals(
            'https://allegro.pl/auth/oauth/authorize?response_type=code&client_id=clientId&redirect_uri=redirectUri',
            $tokenManager->getUri('clientId', 'redirectUri')
        );
    }

    /**
     * @dataProvider errorStatusCodes
     */
    public function testGetTokenThrowsTransferExceptionOnErrorStatusCode($errorStatusCode): void
    {
        $client = new Client;
        $client->addResponse(new Response($errorStatusCode));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $this->expectException(TransferException::class);
        $tokenManager->getToken('clientId', 'clientSecret', 'redirectUri', 'code');
        
    }

    public function errorStatusCodes(): array
    {
        return [[400], [401], [402], [403], [404], [405], [500], [501], [502], [503], [504], [505]];
    }

    /**
     * @dataProvider invalidBodies
     */

    public function testGetTokenThrowsTransferExceptionOnInvalidBody($body): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $this->expectException(TransferException::class);
        $tokenManager->getToken('clientId', 'clientSecret', 'redirectUri', 'code');
        
    }

    public function invalidBodies(): array
    {
        return [['invalid'], [''], ['{}'], ['{"access_token":"abc123"}'], [false], [null]];
    }

    /**
     * @dataProvider validResponses
     */
    public function testGetTokenGetsTokenOnValidResponse($body, $accessToken, $refreshToken): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $token = $tokenManager->getToken('clientId', 'clientSecret', 'redirectUri', 'code');

        $this->assertInstanceOf(
            Token::class,
            $token
        );
        $this->assertEquals($accessToken, $token->getAccessToken());
        $this->assertEquals($refreshToken, $token->getRefreshToken());
    }

    public function validResponses(): array
    {
        return [
            ['{"access_token":"accessToken","refresh_token":"refreshToken"}', 'accessToken', 'refreshToken']
        ];
    }
}
