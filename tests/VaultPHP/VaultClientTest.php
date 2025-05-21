<?php
declare(strict_types=1);

namespace Test\VaultPHP;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use VaultPHP\Authentication\AbstractAuthenticationProvider;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Class VaultClientTest
 * @package Test\VaultPHP
 */
final class VaultClientTest extends TestCase
{
    use TestHelperTrait;


    /**
     * @throws VaultException
     * @throws Exception
     */
    public function testAuthProviderGetsClientInjected(): void
    {
        $auth = $this->createMock(AbstractAuthenticationProvider::class);
        $auth->expects($this->once())
            ->method('setVaultClient')
            ->with($this->isInstanceOf(VaultClient::class));
        ;
        $httpClient = $this->createMock(ClientInterface::class);

        new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
    }

    /**
     * @return void
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultHttpException
     */
    public function testRequestWillExtendedWithDefaultVars(): void {
        $auth = new Token('fooToken');

        $httpClient = new Client();
        $response = new Response(200, []);
        $httpClient->addResponse($response);

        $client = new VaultClient($httpClient, $auth, "http://foo.bar:1337");
        $client->sendApiRequest('LOL', '/i/should/be/preserved', EndpointResponse::class, ['dontReplaceMe']);

        $usedRequest = $httpClient->getLastRequest();
        $this->assertEquals('LOL', $usedRequest->getMethod());
        $this->assertEquals('/i/should/be/preserved', $usedRequest->getUri()->getPath());
        $this->assertEquals(json_encode(['dontReplaceMe']), $usedRequest->getBody()->getContents());

        // test default values that should be added
        $this->assertEquals('http', $usedRequest->getUri()->getScheme());
        $this->assertEquals('foo.bar', $usedRequest->getUri()->getHost());
        $this->assertEquals(1337, $usedRequest->getUri()->getPort());

        $this->assertSame('1', $usedRequest->getHeader('X-Vault-Request')[0]);
        $this->assertSame('fooToken', $usedRequest->getHeader('X-Vault-Token')[0]);
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws InvalidDataException
     * @throws VaultException
     * @throws VaultAuthenticationException
     */
    public function testSuccessApiResponse(): void {
        $response = $this->simulateApiResponse(200, '');
        $this->assertInstanceOf(EndpointResponse::class, $response);
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws VaultException
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    public function testEmptyResponse(): void {
        $response = $this->simulateApiResponse(404);
        $this->assertInstanceOf(EndpointResponse::class, $response);
    }

}
