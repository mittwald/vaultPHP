<?php
declare(strict_types=1);

namespace Test\VaultPHP\Exceptions;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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

    public function testWillThrowWhenApiHostMalformed(): void {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('can\'t parse provided apiHost - malformed uri');

        $auth = new Token('fooToken');
        $httpClient = $this->createMock(ClientInterface::class);

        $client = new VaultClient($httpClient, $auth, "imInvalidHost");
        $client->sendApiRequest('LOL', '/i/should/be/preserved', EndpointResponse::class, ['dontReplaceMe']);
    }

    public function testAuthenticateWillThrowWhenNoTokenIsReturned(): void {
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

    public function testWillThrowWhenAPIReturns403(): void {
        $this->expectException(VaultAuthenticationException::class);

        $httpClient = $this->createMock(ClientInterface::class);
        $auth = $this->createMock(AuthenticationProviderInterface::class);
        $auth
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn(false);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }

    public function testSendRequestWillThrow(): void {
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

    public function testWillThrowWhenReturnClassDeclarationIsInvalid(): void {
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
        $client->sendApiRequest('GET', '/foo', MetaData::class, []);
    }

    public function testWillThrowWhenReturnClassDeclarationIsInvalidForBulk(): void {
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

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws Exception
     * @throws InvalidDataException
     * @throws VaultException
     * @throws VaultAuthenticationException
     */
    public function testServerErrorResponse(): void {
        $this->expectException(VaultResponseException::class);
        $this->simulateApiResponse(500);
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws Exception
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    public function testNotHandledStatusCodeResponse(): void {
        $this->expectException(VaultException::class);
        $this->simulateApiResponse(100);
    }

    public function testResponseExceptionHasRequestResponseMeta(): void {
        try {
            $this->simulateApiResponse(555);
        } catch (VaultResponseException $e) {
            $this->assertInstanceOf(RequestInterface::class, $e->getRequest());
            $this->assertInstanceOf(ResponseInterface::class, $e->getResponse());
        } catch (\Throwable $e){
            $this->throwException($e);
        }
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws Exception
     * @throws VaultException
     * @throws VaultAuthenticationException
     */
    public function testInvalidDataResponse(): void {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('looks malformed');

        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'looks malformed',
            ]
        ]));
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws Exception
     * @throws VaultException
     * @throws VaultAuthenticationException
     */
    public function testInvalidDataResponseWillConcatErrorMessages(): void {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionMessage('looks malformed, oh no');

        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'looks malformed',
                'oh no'
            ]
        ]));
    }

    /**
     * @throws Exception
     * @throws VaultHttpException
     * @throws VaultException
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    public function testInvalidRouteResponse(): void {
        $this->expectException(InvalidRouteException::class);
        $this->simulateApiResponse(404, json_encode([
            'errors' => [
                'no handler',
            ]
        ]));
    }

    /**
     * @throws InvalidRouteException
     * @throws Exception
     * @throws VaultHttpException
     * @throws VaultException
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    public function testInvalidKeyNameNotFound(): void {
        $this->expectException(KeyNameNotFoundException::class);
        $this->simulateApiResponse(400, json_encode([
            'errors' => [
                'encryption key not found',
            ]
        ]));
    }
}
