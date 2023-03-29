<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptData;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\EncryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class EncryptDataBulkTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class EncryptDataBulkTest extends AbstractSecretEngineTestCase
{
    public function testApiCall()
    {
        $encryptRequest = new EncryptDataBulkRequest(
            'foobar',
            [
                new EncryptData('foo', 'fooContext', 'fooNonce'),
                new EncryptData('foo2', 'fooContext', 'fooNonce'),
            ]
        );

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/encrypt/foobar',
            $encryptRequest->toArray(),
            [
                'data' => [
                    'batch_results' => [
                        ['ciphertext' => 'foo1'],
                        ['ciphertext' => 'foo2'],
                    ]
                ]
            ]
        );

        $api = new Transit($client);
        $response = $api->encryptDataBulk($encryptRequest);

        $this->assertEquals(count($response), 2);

        /** @var EncryptDataResponse $bulkResponseOne */
        $bulkResponseOne = $response[0];
        $this->assertEquals('foo1', $bulkResponseOne->getCiphertext());

        /** @var EncryptDataResponse $bulkResponseTwo */
        $bulkResponseTwo = $response[1];
        $this->assertEquals('foo2', $bulkResponseTwo->getCiphertext());

    }
}
