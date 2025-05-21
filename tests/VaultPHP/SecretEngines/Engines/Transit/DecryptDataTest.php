<?php
declare(strict_types=1);

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\DecryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class DecryptDataTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class DecryptDataTest extends AbstractSecretEngineTestCase
{
    /**
     * @throws InvalidDataException
     * @throws VaultException
     * @throws InvalidRouteException
     */
    public function testApiCall(): void
    {
        $decryptDataRequest = new DecryptDataRequest('fooName', 'fooCipher');
        $decryptDataRequest->setNonce('fooNonce');
        $decryptDataRequest->setContext('fooContext');

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/decrypt/fooName',
            $decryptDataRequest->toArray(),
            [
                'data' => [
                    'plaintext' => base64_encode('fooBar'),
                ]
            ]
        );

        $api = new Transit($client);
        $response = $api->decryptData($decryptDataRequest);
        $this->assertEquals('fooBar', $response->getPlaintext());
        $this->assertEquals('fooName', $decryptDataRequest->getName());
        $this->assertEquals('fooContext', $decryptDataRequest->getContext());
        $this->assertEquals('fooNonce', $decryptDataRequest->getNonce());
        $this->assertEquals('fooCipher', $decryptDataRequest->getCiphertext());
    }
}
