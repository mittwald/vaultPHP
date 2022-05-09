<?php

namespace Test\VaultPHP\Authentication\Provider;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VaultPHP\Authentication\Provider\AppRole;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Class AppRoleTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class AppRoleTest extends TestCase
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
            ->with('POST', '/v1/auth/approle/login', EndpointResponse::class, ['role_id' => 'foo', 'secret_id' => 'bar'], false)
            ->willReturn($returnResponseClass);

        $AppRoleAuth = new AppRole('foo', 'bar');
        $AppRoleAuth->setVaultClient($clientMock);

        $tokenMeta = $AppRoleAuth->authenticate();

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

        $userPasswordAuth = new AppRole('foo', 'bar');
        $userPasswordAuth->setVaultClient($clientMock);

        $tokenMeta = $userPasswordAuth->authenticate();

        $this->assertFalse( $tokenMeta);
    }

    public function testWillThrowWhenTryingToGetRequestClientBeforeInit()
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Trying to request the VaultClient before initialization');

        $auth = new AppRole('foo', 'bar');
        $auth->getVaultClient();
    }
}
