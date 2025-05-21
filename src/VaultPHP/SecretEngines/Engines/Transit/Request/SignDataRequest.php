<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;

final class SignDataRequest implements ResourceRequestInterface
{
    public const HASH_ALGORITHM_SHA1 = 'sha1';

    public const HASH_ALGORITHM_SHA2_224 = 'sha2-224';

    public const HASH_ALGORITHM_SHA2_256 = 'sha2-265';

    public const HASH_ALGORITHM_SHA2_384 = 'sha2-384';

    public const HASH_ALGORITHM_SHA2_512 = 'sha2-512';

    public const HASH_ALGORITHM_SHA3_224 = 'sha3-224';

    public const HASH_ALGORITHM_SHA3_256 = 'sha3-265';

    public const HASH_ALGORITHM_SHA3_384 = 'sha3-384';

    public const HASH_ALGORITHM_SHA3_512 = 'sha3-512';

    public const SIGNATURE_ALGORITHM_PSS = 'pss';

    public const SIGNATURE_ALGORITHM_PKCS1V15 = 'pkcs1v15';

    /** @var string */
    protected string $key;

    /** @var string */
    protected string $hashAlgorithm;

    /** @var string */
    protected string $input;

    /** @var string */
    protected string $signature_algorithm;

    /**
     * @param string $key
     * @param string $hashAlgorithm
     * @param string $input
     * @param string $signature_algorithm
     */
    public function __construct(
        string $key,
        string $hashAlgorithm,
        string $input,
        string $signature_algorithm = self::SIGNATURE_ALGORITHM_PSS
    ) {
        $this->key = $key;
        $this->hashAlgorithm = $hashAlgorithm;
        $this->input = $input;
        $this->signature_algorithm = $signature_algorithm;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getHashAlgorithm(): string
    {
        return $this->hashAlgorithm;
    }

    /**
     * @return array
     */
    #[\Override]
    public function toArray(): array
    {
        return [
            'input' => $this->input,
            'signature_algorithm' => $this->signature_algorithm
        ];
    }
}
