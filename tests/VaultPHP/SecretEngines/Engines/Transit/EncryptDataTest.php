<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\SecretEngineTest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\EncryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class EncryptDataTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class EncryptDataTest extends SecretEngineTest
{
    public function testApiCall()
    {
        $encryptDataRequest = new EncryptDataRequest(
            'foobar',
            'encryptMe'
        );
        $encryptDataRequest->setContext('fooContext');
        $encryptDataRequest->setNonce('fooNonce');

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
        $this->assertInstanceOf(EncryptDataResponse::class, $response);
        $this->assertEquals('fooCipher', $response->getCiphertext());

        $this->assertEquals('foobar', $encryptDataRequest->getName());
        $this->assertEquals('fooNonce', $encryptDataRequest->getNonce());
        $this->assertEquals('fooContext', $encryptDataRequest->getContext());
        $this->assertEquals(base64_encode('encryptMe'), $encryptDataRequest->getPlaintext());
    }
}
