<?php
declare(strict_types=1);

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface;
use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\SignDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Transit;
use VaultPHP\VaultClient;

/**
 * Key names are interpolated into the request path and must not be able
 * to change the Vault endpoint that is called.
 *
 * @package Test\VaultPHP\SecretEngines\Transit
 */
final class PathSegmentEncodingTest extends AbstractSecretEngineTestCase
{
    /**
     * @return array<string, array{\Closure(Transit, string): mixed}>
     */
    public static function transitCalls(): array
    {
        return [
            'createKey' => [static fn (Transit $api, string $name) => $api->createKey(new CreateKeyRequest($name))],
            'encryptData' => [static fn (Transit $api, string $name) => $api->encryptData(new EncryptDataRequest($name, 'foo'))],
            'encryptDataBulk' => [static fn (Transit $api, string $name) => $api->encryptDataBulk(new EncryptDataBulkRequest($name))],
            'decryptData' => [static fn (Transit $api, string $name) => $api->decryptData(new DecryptDataRequest($name, 'foo'))],
            'decryptDataBulk' => [static fn (Transit $api, string $name) => $api->decryptDataBulk(new DecryptDataBulkRequest($name))],
            'deleteKey' => [static fn (Transit $api, string $name) => $api->deleteKey($name)],
            'updateKeyConfig' => [static fn (Transit $api, string $name) => $api->updateKeyConfig(new UpdateKeyConfigRequest($name))],
            'sign' => [static fn (Transit $api, string $name) => $api->sign(new SignDataRequest($name, SignDataRequest::HASH_ALGORITHM_SHA2_256, 'foo'))],
        ];
    }

    #[DataProvider('transitCalls')]
    public function testDotSegmentKeyNamesAreRejectedBeforeSending(\Closure $call): void
    {
        foreach (['', '.', '..'] as $name) {
            $httpClient = $this->createMock(ClientInterface::class);
            $httpClient->expects($this->never())->method('sendRequest');
            $api = new Transit(new VaultClient($httpClient, new Token('foo'), 'http://iDontCare.de:443'));

            try {
                $call($api, $name);
                $this->fail(sprintf('key name "%s" was not rejected', $name));
            } catch (VaultException $e) {
                $this->assertStringContainsString('Invalid path segment', $e->getMessage());
            }
        }
    }

    public function testKeyNameCanNotTraverseThePath(): void
    {
        $client = $this->createApiClient(
            'DELETE',
            '/v1/transit/keys/..%2F..%2Fsys%2Fmounts%2Ftransit%3Fx%3D1%23f',
            [],
            []
        );

        (new Transit($client))->deleteKey('../../sys/mounts/transit?x=1#f');
    }
}
