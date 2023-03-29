<?php

namespace Test\VaultPHP\SecretEngines;

use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\VaultClient;

/**
 * Class SecretEngineTest
 * @package Test\VaultPHP\SecretEngines
 */
abstract class AbstractSecretEngineTestCase extends TestCase
{
    protected function createApiClient($expectedMethod, $expectedPath, $expectedData, $responseData, $responseStatus = 200)
    {
        $httpMock = $this->createMock(HttpClient::class);
        $httpMock
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function(RequestInterface $request) use ($expectedMethod, $expectedPath, $expectedData) {
                $this->assertEquals($request->getMethod(), $expectedMethod);
                $this->assertEquals($request->getUri()->getPath(), $expectedPath);
                $this->assertEquals($request->getBody()->getContents(), json_encode($expectedData));
                return true;
            }))
            ->willReturn(new Response($responseStatus, [], json_encode($responseData)));

        return new VaultClient($httpMock, new Token('foo'), 'http://iDontCare.de:443');
    }
}
