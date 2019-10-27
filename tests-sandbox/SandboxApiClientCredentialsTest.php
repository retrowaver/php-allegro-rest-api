<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Allegro\REST\Token\ClientCredentialsToken;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Allegro\REST\Sandbox;

final class SandboxApiClientCredentialsTest extends TestCase
{
    public function testGetOffersListingReturnsValidResponse()
    {
        $token = require(__DIR__ . '/cc_token.php');
        $api = new Sandbox(
            new Client
        );
        $api->setToken($token);

        $response = $api->offers->listing->get(['phrase' => 'dell']);

        $this->assertEquals(200, $response->getStatusCode());
    }
}