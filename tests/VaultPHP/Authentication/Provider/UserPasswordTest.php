<?php

namespace Test\VaultPHP\Authentication\Provider;

use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use Test\VaultPHP\TestHelperTrait;
use VaultPHP\Authentication\Provider\Kubernetes;
use VaultPHP\Authentication\Provider\UserPassword;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Class UserPasswordTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class UserPasswordTest extends TestCase
{
    use TestHelperTrait;

    public function testGetToken(): void
    {
        $userPasswordAuth = new UserPassword('foo', 'bar');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode([
            'auth' => [
                'client_token' => 'fooToken',
            ],
        ])));
        new VaultClient($httpClient, $userPasswordAuth, "https://mocked:1337");

        $tokenMeta = $userPasswordAuth->authenticate();

        $this->assertInstanceOf(AuthenticationMetaData::class, $tokenMeta);
        $this->assertEquals('fooToken', $tokenMeta->getClientToken());

        $request = $httpClient->getLastRequest();

        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("/v1/auth/userpass/login/foo", $request->getUri()->getPath());
        $this->assertEquals('{"password":"bar"}',  $request->getBody()->getContents());
    }

    public function testWillReturnNothingWhenTokenReceiveFails(): void
    {
        $userPasswordAuth = new UserPassword('foo', 'bar');
        $this->mockedVaultClient(
            new Response(200, [], json_encode([])),
            $userPasswordAuth
        );

        $tokenMeta = $userPasswordAuth->authenticate();
        $this->assertFalse($tokenMeta);
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    public function testWillThrowWhenTryingToGetRequestClientBeforeInit(): void
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Trying to request the VaultClient before initialization');

        $auth = new UserPassword('foo', 'bar');
        $auth->authenticate();
    }
}
