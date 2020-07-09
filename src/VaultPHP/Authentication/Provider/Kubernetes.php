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
 * Class Kubernetes
 * @package VaultPHP\Authentication\Provider
 */
class Kubernetes extends AbstractAuthenticationProvider
{
    /** @var string */
    private $role;
    /** @var string */
    private $jwt;
    /** @var string  */
    private $endpoint = '/v1/auth/kubernetes/login';

    /**
     * Kubernetes constructor.
     * @param string $role
     * @param string $jwt
     */
    public function __construct($role, $jwt)
    {
        $this->role = $role;
        $this->jwt = $jwt;
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
            $this->endpoint,
            EndpointResponse::class,
            [
                'role' => $this->role,
                'jwt' => $this->jwt,
            ],
            false
        );

        if ($auth = $response->getMetaData()->getAuth()) {
            return new AuthenticationMetaData($auth);
        }

        return false;
    }
}
