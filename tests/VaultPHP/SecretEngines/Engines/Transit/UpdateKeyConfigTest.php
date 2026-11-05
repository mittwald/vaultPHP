<?php
declare(strict_types=1);

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\UpdateKeyConfigResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;
use VaultPHP\VaultClient;

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

    public function testFalseAndZeroValuesAreSentToVault(): void
    {
        $request = new UpdateKeyConfigRequest('foo');
        $request->setDeletionAllowed(false);
        $request->setMinEncryptionVersion(0);
        $request->setExportable(false);
        $request->setAllowPlaintextBackup(false);

        $httpClient = new Client();
        $httpClient->addResponse(new Response(200, [], json_encode(['data' => []])));
        $api = new Transit(new VaultClient($httpClient, new Token('foo'), 'http://iDontCare.de:443'));

        $api->updateKeyConfig($request);

        $sentRequest = $httpClient->getLastRequest();
        $this->assertEquals('/v1/transit/keys/foo/config', $sentRequest->getUri()->getPath());

        $body = json_decode((string) $sentRequest->getBody(), true);
        $this->assertSame(
            [
                'min_encryption_version' => 0,
                'exportable' => false,
                'allow_plaintext_backup' => false,
                'deletion_allowed' => false,
                'name' => 'foo',
            ],
            $body
        );
        // unset (null) values are still omitted
        $this->assertArrayNotHasKey('min_decryption_version', $body);
    }
}
