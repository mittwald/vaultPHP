<?php

namespace Test\VaultPHP\Authentication\Provider;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Test\VaultPHP\TestHelperTrait;
use VaultPHP\Authentication\Provider\Kubernetes;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\VaultClient;

/**
 * Class KubernetesTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class KubernetesTest extends TestCase
{
    use TestHelperTrait;

    public function testGetToken(): void
    {
        $kubernetesAuth = new Kubernetes('foo', 'bar');

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode([
            'auth' => [
                'client_token' => 'fooToken',
            ],
        ])));
        new VaultClient($httpClient, $kubernetesAuth, "https://mocked:1337");

        $tokenMeta = $kubernetesAuth->authenticate();

        $this->assertInstanceOf(AuthenticationMetaData::class, $tokenMeta);
        $this->assertEquals('fooToken', $tokenMeta->getClientToken());

        $request = $httpClient->getLastRequest();

        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("/v1/auth/kubernetes/login", $request->getUri()->getPath());
        $this->assertEquals('{"role":"foo","jwt":"bar"}',  $request->getBody()->getContents());
    }

    public function testWillReturnNothingWhenTokenReceiveFails(): void
    {
        $kubernetesAuth = new Kubernetes('foo', 'bar');
        $this->mockedVaultClient(
            new Response(200, [], json_encode([])),
            $kubernetesAuth
        );

        $this->assertFalse($kubernetesAuth->authenticate());
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

        $auth = new Kubernetes('foo', 'bar');
        $auth->authenticate();
    }
}
