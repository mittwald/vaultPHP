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
final class AppRole extends AbstractAuthenticationProvider
{
    /** @var string */
    private string $roleId;

    /** @var string */
    private string $secretId;

    /** @var string  */
    private string $endpoint = '/v1/auth/approle/login';

    /**
     * AppRole constructor.
     * @param string $roleId
     * @param string $secretId
     */
    public function __construct(string $roleId, string $secretId)
    {
        $this->roleId = $roleId;
        $this->secretId = $secretId;
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
    public function authenticate(): bool|AuthenticationMetaData
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
