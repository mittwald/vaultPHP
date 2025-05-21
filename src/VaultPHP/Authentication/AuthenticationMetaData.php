<?php

namespace VaultPHP\Authentication;

/**
 * Class AuthenticationMetaData
 * @package VaultPHP\Authentication
 */
final class AuthenticationMetaData
{
    /** @var string */
    private string $token = '';

    /**
     * AuthenticationMetaData constructor.
     * @param object|null $fromAuth
     */
    public function __construct(object $fromAuth = null)
    {
        if ($fromAuth) {
            $this->token = (string) $fromAuth->client_token;
        }
    }

    /**
     * @return string
     */
    public function getClientToken(): string
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isClientTokenPresent(): bool
    {
        return !!$this->token;
    }
}
