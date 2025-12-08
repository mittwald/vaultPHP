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
 * Class UserPassword.
 */
final class UserPassword extends AbstractAuthenticationProvider
{
    /** @var string */
    public const string endpoint = '/v1/auth/userpass/login/%s';
    private string $username;
    private string $password;

    /**
     * UserPassword constructor.
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
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
            $this->getAuthUrl(),
            EndpointResponse::class,
            [
                'password' => $this->password,
            ]
        );

        if ($auth = $response->getMetaData()->getAuth()) {
            return new AuthenticationMetaData($auth);
        }

        return false;
    }

    private function getAuthUrl(): string
    {
        return sprintf($this::endpoint, urlencode($this->username));
    }
}
