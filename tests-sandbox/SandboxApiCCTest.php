<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Allegro\REST\Token\Token;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Allegro\REST\Sandbox;

final class SandboxApiCCTest extends TestCase
{
    public function testGetOffersListingReturnsValidResponse()
    {
        $credentials = require(__DIR__ . '/config.php');
        $token = require(__DIR__ . '/cc_token.php');
        $api = new Sandbox(
            new Client,
            null,
            $credentials['clientId'],
            $credentials['clientSecret'],
            $credentials['redirectUri'],
            $token
        );

        $response = $api->offers->listing->get(['phrase' => 'dell']);

        $this->assertEquals(200, $response->getStatusCode());
    }
}