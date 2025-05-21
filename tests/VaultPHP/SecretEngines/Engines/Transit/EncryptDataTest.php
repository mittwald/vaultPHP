<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class EncryptDataTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class EncryptDataTest extends AbstractSecretEngineTestCase
{
    /**
     * @throws InvalidRouteException
     * @throws InvalidDataException
     * @throws VaultException
     */
    public function testApiCall(): void
    {
        $encryptDataRequest = new EncryptDataRequest(
            'foobar',
            'encryptMe'
        );
        $encryptDataRequest->setContext('fooContext');
        $encryptDataRequest->setNonce('fooNonce');
        $encryptDataRequest->setType(EncryptionType::AES_256_GCM_96);

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/encrypt/foobar',
            $encryptDataRequest->toArray(),
            [
                'data' => [
                    'ciphertext' => 'fooCipher'
                ]
            ]
        );

        $api = new Transit($client);

        $response = $api->encryptData($encryptDataRequest);
        $this->assertEquals('fooCipher', $response->getCiphertext());

        $this->assertEquals('foobar', $encryptDataRequest->getName());
        $this->assertEquals('fooNonce', $encryptDataRequest->getNonce());
        $this->assertEquals('fooContext', $encryptDataRequest->getContext());
        $this->assertEquals(EncryptionType::AES_256_GCM_96->value, $encryptDataRequest->getType());
        $this->assertEquals(base64_encode('encryptMe'), $encryptDataRequest->getPlaintext());
    }
}
