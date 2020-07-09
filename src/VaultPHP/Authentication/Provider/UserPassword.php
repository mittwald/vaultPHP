<?php

namespace VaultPHP\Authentication\Provider;

use VaultPHP\Authentication\AbstractAuthenticationProvider;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Response\EndpointResponse;

/**
 * Class UserPassword
 * @package VaultPHP\Authentication\Provider
 */
class UserPassword extends AbstractAuthenticationProvider
{
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    private $endpoint = '/v1/auth/userpass/login/%s';

    /**
     * UserPassword constructor.
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return bool|AuthenticationMetaData
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultHttpException
     */
    public function authenticate()
    {
        /** @var EndpointResponse $response */
        $response = $this->getVaultClient()->sendApiRequest(
            'POST',
            $this->getAuthUrl(),
            EndpointResponse::class,
            [
                'password' => $this->password
            ],
            false
        );

        if ($auth = $response->getMetaData()->getAuth()) {
            return new AuthenticationMetaData($auth);
        }

        return false;
    }

    /**
     * @return string
     */
    private function getAuthUrl()
    {
        return sprintf($this->endpoint, urlencode($this->username));
    }
}
