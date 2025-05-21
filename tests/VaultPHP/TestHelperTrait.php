<?php

namespace Test\VaultPHP;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\MockObject\Exception;
use VaultPHP\Authentication\AuthenticationProviderInterface;
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
     * @throws VaultHttpException
     */
    private function simulateApiResponse(int $responseStatus, string $responseBody = '', array $responseHeader = []): mixed
    {
        $response = new Response($responseStatus, $responseHeader, $responseBody);
        $auth = new Token('fooToken');

        $httpClient = new Client();
        $httpClient->addResponse($response);

        $client = new VaultClient($httpClient, $auth, TEST_VAULT_ENDPOINT);
        return $client->sendApiRequest("GET", "/mocked", EndpointResponse::class);
    }

    private function mockedVaultClient(Response $response, AuthenticationProviderInterface $auth = new Token("fakeToken")): VaultClient {
        $httpClient = new Client();
        $httpClient->addResponse($response);
        return new VaultClient($httpClient, $auth, "http://mocked:1337");
    }
}
