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
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    #[\Override]
    public function authenticate(): AuthenticationMetaData
    {
        return new AuthenticationMetaData((object) [
            'client_token' => $this->token,
        ]);
    }
}
