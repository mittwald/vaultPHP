<?php

namespace Test\VaultPHP;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\VaultClient;

/**
 * Trait TestHelperTrait
 * @package Test\VaultPHP
 */
trait TestHelperTrait {

    /**
     * @param $responseStatus
     * @param string $responseBody
     * @param array $responseHeader
     * @return mixed
     * @throws \VaultPHP\Exceptions\InvalidDataException
     * @throws \VaultPHP\Exceptions\InvalidRouteException
     * @throws \VaultPHP\Exceptions\VaultAuthenticationException
     * @throws \VaultPHP\Exceptions\VaultException
     * @throws \VaultPHP\Exceptions\VaultHttpException
     */
    private function simulateApiResponse($responseStatus, $responseBody = '', $responseHeader = []) {
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
