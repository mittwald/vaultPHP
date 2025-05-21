<?php
declare(strict_types=1);

namespace VaultPHP\Authentication\Provider;

use VaultPHP\Authentication\AbstractAuthenticationProvider;
use VaultPHP\Authentication\AuthenticationMetaData;

/**
 * Class Token
 * @package VaultPHP\Authentication\Provider
 */
final class Token extends AbstractAuthenticationProvider
{
    /** @var string */
    private string $token;

    /**
     * Token constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return AuthenticationMetaData
     */
    #[\Override]
    public function authenticate(): AuthenticationMetaData
    {
        return new AuthenticationMetaData((object) [
            'client_token' => $this->token,
        ]);
    }
}
