<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\Exceptions\VaultException;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;

final class SignDataRequest implements ResourceRequestInterface
{
    public const string HASH_ALGORITHM_SHA1 = 'sha1';

    public const string HASH_ALGORITHM_SHA2_224 = 'sha2-224';

    public const string HASH_ALGORITHM_SHA2_256 = 'sha2-256';

    public const string HASH_ALGORITHM_SHA2_384 = 'sha2-384';

    public const string HASH_ALGORITHM_SHA2_512 = 'sha2-512';

    public const string HASH_ALGORITHM_SHA3_224 = 'sha3-224';

    public const string HASH_ALGORITHM_SHA3_256 = 'sha3-256';

    public const string HASH_ALGORITHM_SHA3_384 = 'sha3-384';

    public const string HASH_ALGORITHM_SHA3_512 = 'sha3-512';

    public const string SIGNATURE_ALGORITHM_PSS = 'pss';

    public const string SIGNATURE_ALGORITHM_PKCS1V15 = 'pkcs1v15';

    /**
     * Hash algorithms accepted by Vault's transit sign endpoint.
     *
     * The hash algorithm becomes part of the request path, so only these values are allowed.
     * ("none" is left out: Vault only accepts it together with prehashed=true, which this
     * request doesn't support.)
     */
    private const array SUPPORTED_HASH_ALGORITHMS = [
        self::HASH_ALGORITHM_SHA1,
        self::HASH_ALGORITHM_SHA2_224,
        self::HASH_ALGORITHM_SHA2_256,
        self::HASH_ALGORITHM_SHA2_384,
        self::HASH_ALGORITHM_SHA2_512,
        self::HASH_ALGORITHM_SHA3_224,
        self::HASH_ALGORITHM_SHA3_256,
        self::HASH_ALGORITHM_SHA3_384,
        self::HASH_ALGORITHM_SHA3_512,
    ];

    private const array SUPPORTED_SIGNATURE_ALGORITHMS = [
        self::SIGNATURE_ALGORITHM_PSS,
        self::SIGNATURE_ALGORITHM_PKCS1V15,
    ];

    private string $key;

    private string $hashAlgorithm;

    private string $input;

    private string $signature_algorithm;

    /**
     * @throws VaultException if the hash or signature algorithm isn't supported by Vault
     */
    public function __construct(
        string $key,
        string $hashAlgorithm,
        string $input,
        string $signature_algorithm = self::SIGNATURE_ALGORITHM_PSS
    ) {
        if (!in_array($hashAlgorithm, self::SUPPORTED_HASH_ALGORITHMS, true)) {
            throw new VaultException(sprintf(
                'Unsupported hash algorithm, expected one of: %s',
                implode(', ', self::SUPPORTED_HASH_ALGORITHMS)
            ));
        }

        if (!in_array($signature_algorithm, self::SUPPORTED_SIGNATURE_ALGORITHMS, true)) {
            throw new VaultException(sprintf(
                'Unsupported signature algorithm, expected one of: %s',
                implode(', ', self::SUPPORTED_SIGNATURE_ALGORITHMS)
            ));
        }

        $this->key = $key;
        $this->hashAlgorithm = $hashAlgorithm;
        $this->input = $input;
        $this->signature_algorithm = $signature_algorithm;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getHashAlgorithm(): string
    {
        return $this->hashAlgorithm;
    }

    #[\Override]
    public function toArray(): array
    {
        return [
            'input' => $this->input,
            'signature_algorithm' => $this->signature_algorithm,
        ];
    }
}
