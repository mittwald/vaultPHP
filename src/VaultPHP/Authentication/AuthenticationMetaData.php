<?php

namespace VaultPHP\Authentication;

/**
 * Class AuthenticationMetaData
 * @package VaultPHP\Authentication
 */
class AuthenticationMetaData
{
    /** @var string */
    private $token = '';

    /**
     * AuthenticationMetaData constructor.
     * @param null|object $fromAuth
     */
    public function __construct($fromAuth = null)
    {
        if ($fromAuth) {
            /** @var string token */
            $this->token = $fromAuth->client_token;
        }
    }

    /**
     * @return string
     */
    public function getClientToken() {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isClientTokenPresent() {
        return !!$this->token;
    }
}
