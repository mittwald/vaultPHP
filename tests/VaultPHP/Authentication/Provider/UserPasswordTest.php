<?php

namespace Test\VaultPHP\Authentication\Provider;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use VaultPHP\Authentication\Provider\UserPassword;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Class UserPasswordTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class UserPasswordTest extends TestCase
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
            ->with('POST', '/v1/auth/userpass/login/foo', EndpointResponse::class, ['password' => 'bar'], false)
            ->willReturn($returnResponseClass);

        $userPasswordAuth = new UserPassword('foo', 'bar');
        $userPasswordAuth->setVaultClient($clientMock);

        $tokenMeta = $userPasswordAuth->authenticate();

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

        $userPasswordAuth = new UserPassword('foo', 'bar');
        $userPasswordAuth->setVaultClient($clientMock);

        $tokenMeta = $userPasswordAuth->authenticate();

        $this->assertFalse($tokenMeta);
    }

    public function testWillThrowWhenTryingToGetRequestClientBeforeInit()
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Trying to request the VaultClient before initialization');

        $auth = new UserPassword('foo', 'bar');
        $auth->getVaultClient();
    }
}
