<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\CreateKeyResponse;
use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class CreateKeyTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class CreateKeyTest extends AbstractSecretEngineTestCase
{
    public function testApiCall()
    {
        $createKey = new CreateKeyRequest('foobar');
        $createKey->setType(EncryptionType::CHA_CHA_20_POLY_1305);

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/keys/foobar',
            $createKey->toArray(),
            []
        );

        $api = new Transit($client);
        $response = $api->createKey($createKey);

        $this->assertInstanceOf(CreateKeyResponse::class, $response);

        $this->assertEquals('foobar', $createKey->getName());
        $this->assertEquals(EncryptionType::CHA_CHA_20_POLY_1305, $createKey->getType());
    }
}
