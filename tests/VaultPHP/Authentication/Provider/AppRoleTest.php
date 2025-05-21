<?php
declare(strict_types=1);

namespace Test\VaultPHP\Authentication\Provider;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Test\VaultPHP\TestHelperTrait;
use VaultPHP\Authentication\Provider\AppRole;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\VaultClient;

/**
 * Class AppRoleTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class AppRoleTest extends TestCase
{
    use TestHelperTrait;

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws InvalidDataException
     * @throws VaultException
     * @throws VaultAuthenticationException
     */
    public function testGetToken(): void
    {
        $AppRoleAuth = new AppRole('foo', 'bar');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode([
            'auth' => [
                'client_token' => 'fooToken',
            ],
        ])));
        new VaultClient($httpClient, $AppRoleAuth, "https://mocked:1337");

        $tokenMeta = $AppRoleAuth->authenticate();

        $this->assertInstanceOf(AuthenticationMetaData::class, $tokenMeta);
        $this->assertEquals('fooToken', $tokenMeta->getClientToken());

        $request = $httpClient->getLastRequest();

        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("/v1/auth/approle/login", $request->getUri()->getPath());
        $this->assertEquals('{"role_id":"foo","secret_id":"bar"}',  $request->getBody()->getContents());
    }

    public function testWillReturnNothingWhenTokenReceiveFails(): void
    {
        $userPasswordAuth = new AppRole('foo', 'bar');
        $this->mockedVaultClient(
            new Response(200, [], json_encode([])),
            $userPasswordAuth
        );

        $this->assertFalse($userPasswordAuth->authenticate());
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

        $auth = new AppRole('foo', 'bar');
        $auth->authenticate();
    }
}
