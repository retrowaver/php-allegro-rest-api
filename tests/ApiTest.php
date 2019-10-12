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
}
