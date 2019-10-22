<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Allegro\REST\Token\AuthorizationCodeToken;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Allegro\REST\Sandbox;

final class SandboxApiAuthorizationCodeTest extends TestCase
{
    protected $api;

    protected function getApi()
    {
        if ($this->api === null) {
            $credentials = require(__DIR__ . '/config.php');
            $this->api = new Sandbox(
                new Client,
                null,
                new AuthorizationCodeToken(
                    $credentials['accessToken'],
                    'refreshToken' // it's not tested yet    
                )
            );
        }

        return $this->api;
    }

    public function testGetOffersListingReturnsValidResponse()
    {
        $response = $this->getApi()->offers->listing->get(['phrase' => 'dell']);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetSellerOffersReturnsValidResponse()
    {
        $response = $this->getApi()->sale->offers->get();
        $this->assertEquals(200, $response->getStatusCode());

        $response = $this->getApi()->sale->offers->get(['name' => 'offer name']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testOfferImageCanBeUploaded()
    {
        $response = $this->getApi()->sale->images->post(
            file_get_contents(__DIR__ . '/_data/sample_image.jpg'),
            ['Content-Type' => 'image/jpeg']
        );
        $this->assertEquals(201, $response->getStatusCode());
    }
}