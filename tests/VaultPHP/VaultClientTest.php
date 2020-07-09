<?php

namespace Test\VaultPHP;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Test\VaultPHP\Mocks\EndpointResponseMock;
use Test\VaultPHP\Mocks\InvalidEndpointResponseMock;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Authentication\AuthenticationProviderInterface;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Exceptions\VaultResponseException;
use VaultPHP\Response\MetaData;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\VaultClient;

/**
 * Class VaultClientTest
 * @package Test\VaultPHP
 */
final class VaultClientTest extends TestCase
{
    public function testAuthProviderGetsClientInjected()
    {
        $auth = new Token('foo');
        $httpClient = $this->createMock(HttpClient::class);
        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);

        $this->assertSame($client, $auth->getVaultClient());
    }

    public function testRequestWillExtendedWithDefaultVars() {
        $auth = new Token('fooToken');

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function(RequestInterface $requestWithDefaults) {
                // test if values from last request are preserved
                $this->assertEquals('LOL', $requestWithDefaults->getMethod());
                $this->assertEquals('/i/should/be/preserved', $requestWithDefaults->getUri()->getPath());
                $this->assertEquals(json_encode(['dontReplaceMe']), $requestWithDefaults->getBody()->getContents());

                // test default values that should be added
                $this->assertEquals('http', $requestWithDefaults->getUri()->getScheme());
                $this->assertEquals('foo.bar', $requestWithDefaults->getUri()->getHost());
                $this->assertEquals(1337, $requestWithDefaults->getUri()->getPort());

                $this->assertSame('1', $requestWithDefaults->getHeader('X-Vault-Request')[0]);
                $this->assertSame('fooToken', $requestWithDefaults->getHeader('X-Vault-Token')[0]);

                return true;
            }))
            ->willReturn(new Response());

        $client = new VaultClient($httpClient, $auth, "http://foo.bar:1337");
        $client->sendApiRequest('LOL', '/i/should/be/preserved', EndpointResponse::class, ['dontReplaceMe']);
    }

    public function testWillThrowWhenApiHostMalformed() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('can\'t parse provided apiHost - malformed uri');

        $auth = new Token('fooToken');
        $httpClient = $this->createMock(HttpClient::class);

        $client = new VaultClient($httpClient, $auth, "imInvalidHost");
        $client->sendApiRequest('LOL', '/i/should/be/preserved', EndpointResponse::class, ['dontReplaceMe']);
    }

    public function testAuthenticateWillThrowWhenNoTokenIsReturned() {
        $this->expectException(VaultAuthenticationException::class);

        $httpClient = $this->createMock(HttpClient::class);
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

        $httpClient = $this->createMock(HttpClient::class);
        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn(null);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testSendRequest() {
        $request = new Request('GET', 'foo');
        $response = new Response();

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                $this->assertInstanceOf(RequestInterface::class, $request);
                return true;
            }))
            ->willReturn($response);

        $auth = $this->createMock(AuthenticationProviderInterface::class);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo',  EndpointResponse::class, [], false);
    }

    public function testSendRequestWillThrow() {
        $this->expectException(VaultHttpException::class);
        $this->expectExceptionMessage('foobarMessage');

        $httpClient = $this->createMock(HttpClient::class);
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

        $httpClient = $this->createMock(HttpClient::class);
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
        $client->sendApiRequest('GET', '/foo', [], MetaData::class);
    }

    public function testWillThrowWhenReturnClassDeclarationIsInvalidForBulk() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Return Class declaration lacks static::fromBulkResponse');

        $httpClient = $this->createMock(HttpClient::class);
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

        $httpClient = $this->createMock(HttpClient::class);
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

    private function simulateApiResponse($responseStatus, $responseBody = '', $responseHeader = []) {
        $response = new Response($responseStatus, $responseHeader, $responseBody);
        $auth = new Token('fooToken');

        $httpClient = $this->createMock(HttpClient::class);

        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        return $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testSuccessApiResponse() {
        $response = $this->simulateApiResponse(200, '');
        $this->assertInstanceOf(EndpointResponse::class, $response);
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

    public function testEmptyResponse() {
        $response = $this->simulateApiResponse(404);
        $this->assertInstanceOf(EndpointResponse::class, $response);
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
}
