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
    public function __construct(#[\SensitiveParameter] ?object $fromAuth = null)
    {
        if ($fromAuth) {
            $this->token = (string) $fromAuth->client_token;
        }
    }

    /**
     * Keeps the client token out of var_dump()/print_r() output.
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return [
            'token' => $this->isClientTokenPresent() ? '[REDACTED]' : '',
        ];
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
