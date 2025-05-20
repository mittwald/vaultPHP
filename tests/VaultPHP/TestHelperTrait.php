<?php

namespace Test\VaultPHP;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Client\ClientInterface;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Trait TestHelperTrait
 * @package Test\VaultPHP
 */
trait TestHelperTrait {

    /**
     * @param int $responseStatus
     * @param string $responseBody
     * @param array $responseHeader
     * @return mixed
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultHttpException|Exception
     */
    private function simulateApiResponse(int $responseStatus, string $responseBody = '', array $responseHeader = []): mixed
    {
        $response = new Response($responseStatus, $responseHeader, $responseBody);
        $auth = new Token('fooToken');

        $httpClient = $this->createMock(ClientInterface::class);

        $httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        return $client->sendApiRequest('GET', '/foo', EndpointResponse::class);
    }
}
