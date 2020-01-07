<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\Allegro\REST\Token\TokenManager\ClientCredentialsTokenManager;
use Retrowaver\Allegro\REST\Token\Token;
use Http\Mock\Client;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\TransferException;
use Retrowaver\Allegro\REST\Token\ClientCredentialsTokenInterface;
use Retrowaver\Allegro\REST\Token\Credentials;

final class ClientCredentialsTokenManagerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            ClientCredentialsTokenManager::class,
            new ClientCredentialsTokenManager(new Client)
        );
    }

    /**
     * @dataProvider errorStatusCodes
     */
    public function testGetTokenThrowsTransferExceptionOnErrorStatusCode($errorStatusCode): void
    {
        $client = new Client;
        $client->addResponse(new Response($errorStatusCode));

        $clientCredentialsTokenManager = new ClientCredentialsTokenManager($client);

        $this->expectException(TransferException::class);
        $clientCredentialsTokenManager->getClientCredentialsToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ])
        );
        
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

        $clientCredentialsTokenManager = new ClientCredentialsTokenManager($client);

        $this->expectException(TransferException::class);
        $clientCredentialsTokenManager->getClientCredentialsToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ])
        );
        
    }

    public function invalidBodies(): array
    {
        return [['invalid'], [''], ['{}'], ['{"invalid":"abc123"}'], [false], [null]];
    }

    /**
     * @dataProvider validResponses
     */
    public function testGetTokenGetsTokenOnValidResponse($body, $accessToken, $expiresIn): void
    {
        $client = new Client;
        $client->addResponse(new Response(200, [], $body));

        $clientCredentialsTokenManager = new ClientCredentialsTokenManager($client);

        $token = $clientCredentialsTokenManager->getClientCredentialsToken(
            new Credentials([
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'redirectUri' => 'redirectUri'
            ])
        );

        $this->assertInstanceOf(
            ClientCredentialsTokenInterface::class,
            $token
        );
        $this->assertEquals($accessToken, $token->getAccessToken());
        $this->assertEquals($expiresIn, $token->getExpiresIn());
    }

    public function validResponses(): array
    {
        return [
            ['{"access_token":"accessToken","expires_in":12345}', 'accessToken', 12345]
        ];
    }
}
