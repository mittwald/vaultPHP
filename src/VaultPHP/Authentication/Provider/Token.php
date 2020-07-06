<?php

namespace VaultPHP\Authentication\Provider;

use VaultPHP\Authentication\AbstractAuthenticationProvider;
use VaultPHP\Authentication\AuthenticationMetaData;

/**
 * Class Token
 * @package VaultPHP\Authentication\Provider
 */
class Token extends AbstractAuthenticationProvider
{
    /** @var string */
    private $token;

    /**
     * Token constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return AuthenticationMetaData
     */
    public function authenticate()
    {
        return new AuthenticationMetaData((object) [
            'client_token' => $this->token,
        ]);
    }
}
