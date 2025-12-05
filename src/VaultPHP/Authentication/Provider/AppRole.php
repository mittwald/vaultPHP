<?php

declare(strict_types=1);

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
 * Class AppRole.
 */
final class AppRole extends AbstractAuthenticationProvider
{
    /** @var string */
    public const string endpoint = '/v1/auth/approle/login';
    private string $roleId;

    private string $secretId;

    /**
     * AppRole constructor.
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
    public function authenticate(): AuthenticationMetaData|bool
    {
        /** @var EndpointResponse $response */
        $response = $this->sendApiRequest(
            'POST',
            $this::endpoint,
            EndpointResponse::class,
            [
                'role_id' => $this->roleId,
                'secret_id' => $this->secretId,
            ]
        );

        if ($auth = $response->getMetaData()->getAuth()) {
            return new AuthenticationMetaData($auth);
        }

        return false;
    }
}
