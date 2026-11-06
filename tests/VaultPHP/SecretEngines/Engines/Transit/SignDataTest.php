<?php
declare(strict_types=1);

namespace Test\VaultPHP\SecretEngines\Engines\Transit;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface;
use Test\VaultPHP\SecretEngines\AbstractSecretEngineTestCase;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Engines\Transit\Request\SignDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\SignDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Transit;
use VaultPHP\VaultClient;

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

    public function testHashAlgorithmConstantsMatchVault(): void
    {
        $this->assertSame('sha1', SignDataRequest::HASH_ALGORITHM_SHA1);
        $this->assertSame('sha2-224', SignDataRequest::HASH_ALGORITHM_SHA2_224);
        $this->assertSame('sha2-256', SignDataRequest::HASH_ALGORITHM_SHA2_256);
        $this->assertSame('sha2-384', SignDataRequest::HASH_ALGORITHM_SHA2_384);
        $this->assertSame('sha2-512', SignDataRequest::HASH_ALGORITHM_SHA2_512);
        $this->assertSame('sha3-224', SignDataRequest::HASH_ALGORITHM_SHA3_224);
        $this->assertSame('sha3-256', SignDataRequest::HASH_ALGORITHM_SHA3_256);
        $this->assertSame('sha3-384', SignDataRequest::HASH_ALGORITHM_SHA3_384);
        $this->assertSame('sha3-512', SignDataRequest::HASH_ALGORITHM_SHA3_512);
    }

    public static function supportedHashAlgorithms(): array
    {
        return [
            [SignDataRequest::HASH_ALGORITHM_SHA1],
            [SignDataRequest::HASH_ALGORITHM_SHA2_224],
            [SignDataRequest::HASH_ALGORITHM_SHA2_256],
            [SignDataRequest::HASH_ALGORITHM_SHA2_384],
            [SignDataRequest::HASH_ALGORITHM_SHA2_512],
            [SignDataRequest::HASH_ALGORITHM_SHA3_224],
            [SignDataRequest::HASH_ALGORITHM_SHA3_256],
            [SignDataRequest::HASH_ALGORITHM_SHA3_384],
            [SignDataRequest::HASH_ALGORITHM_SHA3_512],
        ];
    }

    #[DataProvider('supportedHashAlgorithms')]
    public function testSupportedHashAlgorithmIsUsedAsPathSegment(string $hashAlgorithm): void
    {
        $client = $this->createApiClient(
            'POST',
            '/v1/transit/sign/test/' . $hashAlgorithm,
            [
                'input' => 'some-input-to-sign',
                'signature_algorithm' => 'pkcs1v15'
            ],
            [
                'data' => [
                    'signature' => 'vault:v1:someHash',
                ]
            ]
        );

        $request = new SignDataRequest(
            'test',
            $hashAlgorithm,
            'some-input-to-sign',
            SignDataRequest::SIGNATURE_ALGORITHM_PKCS1V15
        );

        $response = (new Transit($client))->sign($request);
        $this->assertEquals('vault:v1:someHash', $response->getSignature());
    }

    public static function unsupportedHashAlgorithms(): array
    {
        return [
            'path traversal to other key' => ['../../keys/victim-key/rotate'],
            'path traversal to auth backend' => ['../../../auth/token/revoke-self'],
            'query and fragment' => ['sha2-256?x=1#frag'],
            'trailing path' => ['sha2-256/../../keys/victim-key'],
            'encoded traversal' => ['%2e%2e%2fkeys'],
            'dot segment' => ['..'],
            'empty' => [''],
            'wrong case' => ['SHA2-256'],
            'old typo value' => ['sha2-265'],
            'none (requires prehashed input)' => ['none'],
        ];
    }

    #[DataProvider('unsupportedHashAlgorithms')]
    public function testUnsupportedHashAlgorithmIsRejected(string $hashAlgorithm): void
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Unsupported hash algorithm');

        new SignDataRequest('test', $hashAlgorithm, 'some-input-to-sign');
    }

    public function testUnsupportedSignatureAlgorithmIsRejected(): void
    {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Unsupported signature algorithm');

        new SignDataRequest('test', SignDataRequest::HASH_ALGORITHM_SHA2_256, 'some-input-to-sign', 'foo');
    }

    public function testKeyNameIsEncodedAsSinglePathSegment(): void
    {
        $client = $this->createApiClient(
            'POST',
            '/v1/transit/sign/victim%3Fx%3D1%23f/sha2-256',
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

        $request = new SignDataRequest('victim?x=1#f', SignDataRequest::HASH_ALGORITHM_SHA2_256, 'some-input-to-sign');
        (new Transit($client))->sign($request);
    }

    public static function invalidKeyNames(): array
    {
        return [
            'dot segment' => ['..'],
            'slash' => ['victim/sha1'],
            'traversal' => ['../../keys/victim/rotate'],
        ];
    }

    #[DataProvider('invalidKeyNames')]
    public function testInvalidKeyNameIsRejectedBeforeSending(string $keyName): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $client = new VaultClient($httpClient, new Token('foo'), 'http://iDontCare.de:443');

        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('Invalid path segment');

        $request = new SignDataRequest($keyName, SignDataRequest::HASH_ALGORITHM_SHA2_256, 'some-input-to-sign');
        (new Transit($client))->sign($request);
    }
}
