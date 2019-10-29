<?php
namespace Allegro\REST\Token;

class ClientCredentialsToken implements ClientCredentialsTokenInterface
{
    use TokenTrait;

    public function __construct(string $accessToken, int $expiresIn)
    {
        $this
            ->setAccessToken($accessToken)
            ->setExpiresIn($expiresIn)
        ;
    }
}