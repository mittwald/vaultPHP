<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\SecretEngines\Engines\Transit\Request\SignDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\SignDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class SignDataTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class SignDataTest extends AbstractSecretEngineTestCase
{
    public function testApiCall()
    {
        $client = $this->createApiClient(
            'POST',
            '/v1/transit/sign/test/sha1',
            [
                'input' => 'some-input-to-sign',
                'signature_algorithm' => 'pss'
            ],
            [
                'data' => [
                    'signature' => 'vault:v1:someHash',
                ]
            ]
        );
        $request = new SignDataRequest(
            'test',
            SignDataRequest::HASH_ALGORITHM_SHA1,
            'some-input-to-sign',
            SignDataRequest::SIGNATURE_ALGORITHM_PSS
        );
        $api = new Transit($client);
        $response = $api->sign($request);

        $this->assertInstanceOf(SignDataResponse::class, $response);
        $this->assertEquals('vault:v1:someHash', $response->getSignature());
    }
}
