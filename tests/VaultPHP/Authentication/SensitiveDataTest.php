<?php
declare(strict_types=1);

namespace Test\VaultPHP\Authentication;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Authentication\AuthenticationProviderInterface;
use VaultPHP\Authentication\Provider\AppRole;
use VaultPHP\Authentication\Provider\Kubernetes;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Authentication\Provider\UserPassword;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\SecretEngines\Engines\Transit\Transit;
use VaultPHP\VaultClient;

/**
 * Credentials must not end up in exception traces or debug output.
 *
 * @package Test\VaultPHP\Authentication
 */
final class SensitiveDataTest extends TestCase
{
    private const string SECRET = 'very-secret-credential-value';

    private string|false $previousIgnoreArgs = false;

    protected function setUp(): void
    {
        // make sure arguments are part of exception traces (the PHP default without php.ini)
        $this->previousIgnoreArgs = ini_set('zend.exception_ignore_args', '0');
    }

    protected function tearDown(): void
    {
        if (false !== $this->previousIgnoreArgs) {
            ini_set('zend.exception_ignore_args', $this->previousIgnoreArgs);
        }
    }

    /**
     * @return array<string, array{AuthenticationProviderInterface, string}>
     */
    public static function loginProviders(): array
    {
        return [
            'AppRole' => [new AppRole(self::SECRET, self::SECRET), '/v1/auth/approle/login'],
            'UserPassword' => [new UserPassword('foo', self::SECRET), '/v1/auth/userpass/login/foo'],
            'Kubernetes' => [new Kubernetes('foo', self::SECRET), '/v1/auth/kubernetes/login'],
        ];
    }

    #[DataProvider('loginProviders')]
    public function testCredentialsAreNotPartOfExceptionTraces(AuthenticationProviderInterface $provider, string $loginEndpoint): void
    {
        $httpClient = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new \RuntimeException('connection refused');
            }
        };

        $client = new VaultClient($httpClient, $provider, 'https://vault.invalid');

        try {
            (new Transit($client))->listKeys();
            $this->fail('expected the login to fail');
        } catch (VaultAuthenticationException $e) {
            // arguments are recorded at all - otherwise this test would prove nothing
            $this->assertTrue(self::traceArgsContain($e, $loginEndpoint));
            $this->assertFalse(self::traceArgsContain($e, self::SECRET));
        }
    }

    #[DataProvider('loginProviders')]
    public function testDebugOutputDoesNotContainCredentials(AuthenticationProviderInterface $provider, string $loginEndpoint): void
    {
        $httpClient = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new Response(200, [], json_encode([
                    'auth' => ['client_token' => 'client-token-' . SensitiveDataTest::class],
                    'data' => ['keys' => []],
                ]));
            }
        };

        $client = new VaultClient($httpClient, $provider, 'https://vault.invalid');
        (new Transit($client))->listKeys();

        $this->assertNotNull($client->authenticationMetaData);
        $this->assertStringStartsWith('/v1/auth/', $loginEndpoint);
        $this->assertDebugOutputDoesNotContain($client, self::SECRET);
        $this->assertDebugOutputDoesNotContain($client, 'client-token-' . SensitiveDataTest::class);
        $this->assertDebugOutputDoesNotContain($provider, self::SECRET);
    }

    public function testTokenIsRedactedInDebugOutput(): void
    {
        $this->assertDebugOutputDoesNotContain(new Token(self::SECRET), self::SECRET);
        $this->assertDebugOutputDoesNotContain(
            new AuthenticationMetaData((object) ['client_token' => self::SECRET]),
            self::SECRET
        );
    }

    private function assertDebugOutputDoesNotContain(object $subject, string $needle): void
    {
        ob_start();
        var_dump($subject);
        $varDump = (string) ob_get_clean();

        $this->assertStringContainsString('[REDACTED]', $varDump);
        $this->assertStringNotContainsString($needle, $varDump);
        $this->assertStringNotContainsString($needle, print_r($subject, true));
    }

    private static function traceArgsContain(\Throwable $exception, string $needle): bool
    {
        for ($current = $exception; null !== $current; $current = $current->getPrevious()) {
            foreach ($current->getTrace() as $frame) {
                $args = $frame['args'] ?? [];
                $found = false;
                array_walk_recursive($args, static function (mixed $value) use ($needle, &$found): void {
                    if (is_string($value) && str_contains($value, $needle)) {
                        $found = true;
                    }
                });

                if ($found) {
                    return true;
                }
            }
        }

        return false;
    }
}
