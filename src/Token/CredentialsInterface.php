<?php
namespace Allegro\REST\Token;

interface CredentialsInterface
{
    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @return string
     */
    public function getRedirectUri(): string;

    /**
     * @param string $clientId
     * @return CredentialsInterface
     */
    public function setClientId(string $clientId): CredentialsInterface;

    /**
     * @param string $clientSecret
     * @return CredentialsInterface
     */
    public function setClientSecret(string $clientSecret): CredentialsInterface;

    /**
     * @param string $redirectUri
     * @return CredentialsInterface
     */
    public function setRedirectUri(string $redirectUri): CredentialsInterface;
}
