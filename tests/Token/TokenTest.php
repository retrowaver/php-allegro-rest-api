<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Allegro\REST\Token\Token;

final class TokenTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Token::class,
            new Token('some access token', 'some refresh token')
        );
    }
}
