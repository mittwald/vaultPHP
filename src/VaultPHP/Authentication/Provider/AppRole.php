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
 * Class AppRole
 * @package VaultPHP\Authentication\Provider
 */
class AppRole extends AbstractAuthenticationProvider
{
    /** @var string */
    private $roleId;

    /** @var string */
    private $secretId;

    /** @var string  */
    private $endpoint = '/v1/auth/approle/login';

    /**
     * AppRole constructor.
     * @param $roleId
     * @param $secretId
     */
    public function __construct($roleId, $secretId)
    {
        $this->roleId = $roleId;
        $this->secretId= $secretId;
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
                'role_id' => $this->roleId,
                'secret_id' => $this->secretId,
            ],
            false
        );

        if ($auth = $response->getMetaData()->getAuth()) {
            return new AuthenticationMetaData($auth);
        }

        return false;
    }
}
