<?php

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\UpdateKeyConfigResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;

/**
 * Class UpdateKeyConfigTest
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class UpdateKeyConfigTest extends AbstractSecretEngineTestCase
{
    public function testApiCall()
    {
        $request = new UpdateKeyConfigRequest('foo');
        $request->setDeletionAllowed(true);
        $request->setExportable(true);
        $request->setAllowPlaintextBackup(true);
        $request->setMinDecryptionVersion(1337);
        $request->setMinEncryptionVersion(1338);

        $client = $this->createApiClient(
            'POST',
            '/v1/transit/keys/foo/config',
            $request->toArray(),
            [
                'data' => [
                    'keys' => [
                        'key1',
                        'key2',
                    ]
                ]
            ]
        );

        $api = new Transit($client);

        $response = $api->updateKeyConfig($request);
        $this->assertInstanceOf(UpdateKeyConfigResponse::class, $response);

        $this->assertEquals('foo', $request->getName());
        $this->assertTrue($request->getDeletionAllowed());
        $this->assertTrue($request->getExportable());
        $this->assertTrue($request->getAllowPlaintextBackup());
        $this->assertEquals(1337, $request->getMinDecryptionVersion());
        $this->assertEquals(1338, $request->getMinEncryptionVersion());
    }
}
