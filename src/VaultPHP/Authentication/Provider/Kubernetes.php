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
final class Kubernetes extends AbstractAuthenticationProvider
{
    /** @var string */
    private string $role;
    /** @var string */
    private string $jwt;
    /** @var string  */
    private string $endpoint;

    /**
     * Kubernetes constructor.
     * @param string $role
     * @param string $jwt
     * @param string $authPath - defaults to '/auth/kubernetes'
     */
    public function __construct(string $role, string $jwt, string $authPath = 'auth/kubernetes')
    {
        $this->role = $role;
        $this->jwt = $jwt;
        $this->endpoint = '/v1/' . $authPath . '/login';
    }

    /**
     * @return AuthenticationMetaData|false
     *
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultHttpException
     */
    #[\Override]
    public function authenticate(): false|AuthenticationMetaData
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
