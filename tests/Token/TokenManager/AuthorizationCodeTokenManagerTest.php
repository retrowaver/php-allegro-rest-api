<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\AuthorizationCodeTokenManager;
use Http\Mock\Client;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;
use Allegro\REST\Token\AuthorizationCodeTokenInterface;
use Allegro\REST\Token\AuthorizationCodeToken;
use Allegro\REST\Token\Credentials;

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
            $tokenManager->getUri(
                new Credentials([
                    'clientId' => 'clientId',
                    'clientSecret' => 'clientSecret',
                    'redirectUri' => 'redirectUri'
                ])
            )
        );
    }

    /**
     * @dataProvider errorStatusCodes
     */
    public function testGetAuthCodeTokenThrowsTransferExceptionOnErrorStatusCode($errorStatusCode): void
    {
        $client = new Client;
        $client->addResponse(new Response($errorStatusCode));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $this->expectException(TransferException::class);
        $tokenManager->getAuthorizationCodeToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ]),
            'code'
        );
        
    }

    public function errorStatusCodes(): array
    {
        return [[400], [401], [402], [403], [404], [405], [500], [501], [502], [503], [504], [505]];
    }

    /**
     * @dataProvider invalidBodies
     */

    public function testGetAuthCodeTokenThrowsTransferExceptionOnInvalidBody($body): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $this->expectException(TransferException::class);
        $tokenManager->getAuthorizationCodeToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ]),
            'code'
        );
        
    }

    public function invalidBodies(): array
    {
        return [['invalid'], [''], ['{}'], ['{"access_token":"abc123"}'], [false], [null]];
    }

    /**
     * @dataProvider validResponses
     */
    public function testGetAuthCodeTokenGetsTokenOnValidResponse($body, $accessToken, $refreshToken, $expiresIn): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $token = $tokenManager->getAuthorizationCodeToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ]),
            'code'
        );

        $this->assertInstanceOf(
            AuthorizationCodeTokenInterface::class,
            $token
        );
        $this->assertEquals($accessToken, $token->getAccessToken());
        $this->assertEquals($refreshToken, $token->getRefreshToken());
        $this->assertEquals($expiresIn, $token->getExpiresIn());
    }

    public function validResponses(): array
    {
        return [
            ['{"access_token":"accessToken","refresh_token":"refreshToken","expires_in":12345}', 'accessToken', 'refreshToken', 12345]
        ];
    }

    /**
     * @dataProvider validRefreshTokenResponses
     */
    public function testRefreshTokenRefreshesTokenOnValidResponse($body, $accessToken, $refreshToken, $expiresIn)
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $tokenManager = new AuthorizationCodeTokenManager($client);

        $token = new AuthorizationCodeToken('oldAccessToken', 'oldRefreshToken', 12345);
        $tokenManager->refreshToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ]),
            $token
        );

        $this->assertEquals($accessToken, $token->getAccessToken());
        $this->assertEquals($refreshToken, $token->getRefreshToken());
        $this->assertEquals($expiresIn, $token->getExpiresIn());
    }

    public function validRefreshTokenResponses(): array
    {
        return [
            ['{"access_token":"accessToken","refresh_token":"refreshToken","expires_in":12345}', 'accessToken', 'refreshToken', 12345]
        ];
    }
}
