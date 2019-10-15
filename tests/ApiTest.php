<?php

use PHPUnit\Framework\TestCase;
use Http\Mock\Client;
use Allegro\REST\Api;
use Allegro\REST\Token\Token;

final class ApiTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Api::class,
            new Api(
                new Client,
                null,
                new Token('accessToken', 'refreshToken'),
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
            new Token('accessToken', 'refreshToken'),
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
}
