<?php

namespace Test\VaultPHP\Response;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Test\VaultPHP\Mocks\InvalidEndpointResponseMock;
use Test\VaultPHP\TestHelperTrait;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Authentication\AuthenticationProviderInterface;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\KeyNameNotFoundException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Exceptions\VaultResponseException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\Response\MetaData;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\VaultClient;

/**
 * Class ExceptionsTest
 * @package Test\VaultPHP\Response
 */
final class ExceptionsTest extends TestCase
{
    use TestHelperTrait;

    public function testWillThrowWhenApiHostMalformed() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('can\'t parse provided apiHost - malformed uri');

        $auth = new Token('fooToken');
        $httpClient = $this->createMock(ClientInterface::class);

        $client = new VaultClient($httpClient, $auth, "imInvalidHost");
        $client->sendApiRequest('LOL', '/i/should/be/preserved', EndpointResponse::class, ['dontReplaceMe']);
    }

    public function testAuthenticateWillThrowWhenNoTokenIsReturned() {
        $this->expectException(VaultAuthenticationException::class);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(403));

        $auth = new Token("fooBar");

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testWillThrowWhenAPIReturns403() {
        $this->expectException(VaultAuthenticationException::class);

        $httpClient = $this->createMock(ClientInterface::class);
        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn(null);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testSendRequestWillThrow() {
        $this->expectException(VaultHttpException::class);
        $this->expectExceptionMessage('foobarMessage');

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willThrowException(new \Exception('foobarMessage'));

        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth->expects($this->once())
            ->method('authenticate')
            ->willReturn(new AuthenticationMetaData((object) [
                'client_token' => 'foo',
            ]));

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testWillThrowWhenReturnClassDeclarationIsInvalid() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Return Class declaration lacks static::fromResponse');

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200));

        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth->expects($this->once())
            ->method('authenticate')
            ->willReturn(new AuthenticationMetaData((object) [
                'client_token' => 'foo',
            ]));

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', "", MetaData::class);
    }

    public function testWillThrowWhenReturnClassDeclarationIsInvalidForBulk() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Return Class declaration lacks static::fromBulkResponse');

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200));

        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth->expects($this->once())
            ->method('authenticate')
            ->willReturn(new AuthenticationMetaData((object) [
                'client_token' => 'foo',
            ]));

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', MetaData::class, new EncryptDataBulkRequest('foo'));
    }

    public function testWillThrowWhenResultOfReturnClassDeclarationIsInvalid() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Result from "fromResponse/fromBulkResponse" isn\'t an instance of EndpointResponse');

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200));

        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth->expects($this->once())
            ->method('authenticate')
            ->willReturn(new AuthenticationMetaData((object) [
                'client_token' => 'foo',
            ]));

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', InvalidEndpointResponseMock::class, []);
    }

    public function testServerErrorResponse() {
        $this->expectException(VaultResponseException::class);
        $this->simulateApiResponse(500);
    }

    public function testNotHandledStatusCodeResponse() {
        $this->expectException(VaultException::class);
        $this->simulateApiResponse(100);
    }

    public function testResponseExceptionHasRequestResponseMeta() {
        try {
            $this->simulateApiResponse(555);
        } catch (VaultResponseException $e) {
            $this->assertInstanceOf(RequestInterface::class, $e->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $e->getResponse());
        }
    }


    public function testInvalidDataResponse() {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('looks malformed');

        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'looks malformed',
            ]
        ]));
    }

    public function testInvalidDataResponseWillConcatErrorMessages() {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('looks malformed, oh no');

        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'looks malformed',
                'oh no'
            ]
        ]));
    }

    public function testInvalidRouteResponse() {
        $this->expectException(InvalidRouteException::class);
        $this->simulateApiResponse(404, json_encode([
            'errors' => [
                'no handler',
            ]
        ]));
    }

    public function testInvalidKeyNameNotFound() {
        $this->expectException(KeyNameNotFoundException::class);
        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'encryption key not found',
            ]
        ]));
    }
}
