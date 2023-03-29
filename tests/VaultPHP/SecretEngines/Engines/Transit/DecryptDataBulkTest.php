<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptData;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\DecryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class DecryptDataBulkTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class DecryptDataBulkTest extends AbstractSecretEngineTestCase
{
    public function testApiCall()
    {
        $decryptDataRequest = new DecryptDataBulkRequest(
            'foobar',
            [
                new DecryptData('foo', 'fooContext', 'fooNonce'),
                new DecryptData('foo2', 'fooContext2', 'fooNonce2'),
            ]
        );

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/decrypt/foobar',
            $decryptDataRequest->toArray(),
            [
                'data' => [
                    'batch_results' => [
                        ['plaintext' => base64_encode('plain')],
                        ['plaintext' => base64_encode('plain2')],
                    ]
                ]
            ]
        );

        $api = new Transit($client);
        $response = $api->decryptDataBulk($decryptDataRequest);

        $this->assertEquals(count($response), 2);

        /** @var DecryptDataResponse $bulkResponseOne */
        $bulkResponseOne = $response[0];
        $this->assertEquals('plain', $bulkResponseOne->getPlaintext());

        /** @var DecryptDataResponse $bulkResponseTwo */
        $bulkResponseTwo = $response[1];
        $this->assertEquals('plain2', $bulkResponseTwo->getPlaintext());

    }
}
