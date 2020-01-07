<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Retrowaver\Allegro\REST\Token\TokenManager\Sandbox\SandboxClientCredentialsTokenManager;
use Retrowaver\Allegro\REST\Token\AuthorizationCodeToken;
use Http\Adapter\Guzzle6\Client;
use Http\Client\Exception\TransferException;
use Retrowaver\Allegro\REST\Sandbox;

final class SandboxApiAuthorizationCodeTest extends TestCase
{
    protected $api;

    protected function getApi()
    {
        if ($this->api === null) {
            $config = require(__DIR__ . '/config.php');
            $this->api = new Sandbox(
                new Client
            );

            $this->api->setToken(
                new AuthorizationCodeToken(
                    $config['accessToken'],
                    'refreshToken', // placeholder value because it's not used in tests
                    12345 // placeholder
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