<?php

namespace Test\VaultPHP\Authentication\Provider;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VaultPHP\Authentication\Provider\Kubernetes;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Class KubernetesTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class KubernetesTest extends TestCase
{
    public function testGetToken()
    {
        $apiResponse = new Response(200, [], json_encode([
            'auth' => [
                'client_token' => 'fooToken',
            ],
        ]));
        $returnResponseClass = EndpointResponse::fromResponse($apiResponse);

        $clientMock = $this->createMock(VaultClient::class);
        $clientMock
            ->expects($this->once())
            ->method('sendApiRequest')
            ->with('POST', '/v1/auth/kubernetes/login', EndpointResponse::class, ['role' => 'foo', 'jwt' => 'bar'], false)
            ->willReturn($returnResponseClass);

        $kubernetesAuth = new Kubernetes('foo', 'bar');
        $kubernetesAuth->setVaultClient($clientMock);

        $tokenMeta = $kubernetesAuth->authenticate();

        $this->assertInstanceOf(AuthenticationMetaData::class, $tokenMeta);
        $this->assertEquals('fooToken', $tokenMeta->getClientToken());
    }

    public function testWillReturnNothingWhenTokenReceiveFails()
    {
        $apiResponse = new Response(200, [], json_encode([]));
        $returnResponseClass = EndpointResponse::fromResponse($apiResponse);

        $clientMock = $this->createMock(VaultClient::class);
        $clientMock
            ->expects($this->once())
            ->method('sendApiRequest')
            ->willReturn($returnResponseClass);

        $userPasswordAuth = new Kubernetes('foo', 'bar');
        $userPasswordAuth->setVaultClient($clientMock);

        $tokenMeta = $userPasswordAuth->authenticate();

        $this->assertFalse( $tokenMeta);
    }

    public function testWillThrowWhenTryingToGetRequestClientBeforeInit()
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Trying to request the VaultClient before initialization');

        $auth = new Kubernetes('foo', 'bar');
        $auth->getVaultClient();
    }
}
