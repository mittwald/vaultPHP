<?php

declare(strict_types=1);

namespace VaultPHP\Authentication\Provider;

use VaultPHP\Authentication\AbstractAuthenticationProvider;
use VaultPHP\Authentication\AuthenticationMetaData;

/**
 * Class Token.
 */
final class Token extends AbstractAuthenticationProvider
{
    private string $token;

    /**
     * Token constructor.
     */
    public function __construct(#[\SensitiveParameter] string $token)
    {
        $this->token = $token;
    }

    /**
     * Keeps the token out of var_dump()/print_r() output.
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return [
            'token' => '[REDACTED]',
        ];
    }

    #[\Override]
    public function authenticate(): AuthenticationMetaData
    {
        return new AuthenticationMetaData((object) [
            'client_token' => $this->token,
        ]);
    }
}
