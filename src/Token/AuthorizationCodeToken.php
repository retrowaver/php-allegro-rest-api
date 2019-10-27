<?php
namespace Allegro\REST\Token;

class AuthorizationCodeToken implements AuthorizationCodeTokenInterface
{
    use TokenTrait;
    use RefreshableTokenTrait;

    public function __construct(string $accessToken, string $refreshToken, int $expiresIn)
    {
        $this
            ->setAccessToken($accessToken)
            ->setRefreshToken($refreshToken)
            ->setExpiresIn($expiresIn)
        ;
    }
}
