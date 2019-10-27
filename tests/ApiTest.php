<?php

use PHPUnit\Framework\TestCase;
use Http\Mock\Client;
use Allegro\REST\Api;
use Allegro\REST\Token\ClientCredentialsToken;
use Allegro\REST\Token\Credentials;

final class ApiTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Api::class,
            new Api(
                new Client,
                null,
                new ClientCredentialsToken('accessToken'),
                []
            )
        );
    }

    public function testCommandPutRequestContainsCorrectUriPath()
    {
        $client = new Client;
        $api = new Api(
            $client,
            null,
            new ClientCredentialsToken('accessToken'),
            []
        );

        $api->sale->{'offer-publication-commands'}()->put([]);
        $this->assertRegExp(
            '/^\/sale\/offer-publication-commands\/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/',
            $client->getLastRequest()->getUri()->getPath()
        );

        $api->foo->bar->baz->{'foo-commands'}()->put([]);
        $this->assertRegExp(
            '/^\/foo\/bar\/baz\/foo-commands\/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/',
            $client->getLastRequest()->getUri()->getPath()
        );

        $api->{'foo-commands'}('custom-uuid')->put([]);
        $this->assertRegExp(
            '/^\/foo-commands\/custom-uuid$/',
            $client->getLastRequest()->getUri()->getPath()
        );
    }

    public function testGetCustomHeadersReturnsValuesFromConst()
    {
        $api = new Api(
            null,
            null,
            new ClientCredentialsToken('accessToken'),
            []
        );

        $this->assertEqualsCanonicalizing(
            Api::CUSTOM_HEADERS,
            $api->getCustomHeaders()
        );
    }

    public function testSetCustomHeadersReplacesCustomHeaders()
    {
        $api = new Api(
            null,
            null,
            new ClientCredentialsToken('accessToken'),
            []
        );

        $api->setCustomHeaders(['Header' => 'value']);
        $this->assertEqualsCanonicalizing(
            ['Header' => 'value'],
            $api->getCustomHeaders()
        );
    }

    public function testAddCustomHeaderAddsCustomHeader()
    {
        $api = new Api(
            null,
            null,
            new ClientCredentialsToken('accessToken'),
            []
        );

        $before = $api->getCustomHeaders();
        $api->addCustomHeader(['Some-Header' => 'some_value']);
        $after = $api->getCustomHeaders();

        $this->assertNotEqualsCanonicalizing($before, $after);
        $this->assertEquals('some_value', $after['Some-Header']);
    }

    public function testAddCustomHeaderDoesntReplaceExistingCustomHeader()
    {
        $api = new Api(
            null,
            null,
            new ClientCredentialsToken('accessToken'),
            []
        );

        $api->addCustomHeader(['Some-Header' => 'some_value']);
        $before = $api->getCustomHeaders();
        $api->addCustomHeader(['Some-Header' => 'new_value']);
        $after = $api->getCustomHeaders();

        $this->assertEqualsCanonicalizing($before, $after);
    }
}
