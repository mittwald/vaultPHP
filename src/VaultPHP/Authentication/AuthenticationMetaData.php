<?php

declare(strict_types=1);

namespace VaultPHP\Authentication;

/**
 * Class AuthenticationMetaData.
 */
final class AuthenticationMetaData
{
    private string $token = '';

    /**
     * AuthenticationMetaData constructor.
     */
    public function __construct(?object $fromAuth = null)
    {
        if ($fromAuth) {
            $this->token = (string) $fromAuth->client_token;
        }
    }

    public function getClientToken(): string
    {
        return $this->token;
    }

    public function isClientTokenPresent(): bool
    {
        return (bool) $this->token;
    }
}
