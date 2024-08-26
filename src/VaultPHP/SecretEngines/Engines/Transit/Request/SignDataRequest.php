<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;

final class SignDataRequest  implements ResourceRequestInterface
{
    const HASH_ALGORITHM_SHA1 = 'sha1';

    const HASH_ALGORITHM_SHA2_224 = 'sha2-224';

    const HASH_ALGORITHM_SHA2_256 = 'sha2-265';

    const HASH_ALGORITHM_SHA2_384 = 'sha2-384';

    const HASH_ALGORITHM_SHA2_512 = 'sha2-512';

    const HASH_ALGORITHM_SHA3_224 = 'sha3-224';

    const HASH_ALGORITHM_SHA3_256 = 'sha3-265';

    const HASH_ALGORITHM_SHA3_384 = 'sha3-384';

    const HASH_ALGORITHM_SHA3_512 = 'sha3-512';

    const SIGNATURE_ALGORITHM_PSS = 'pss';

    const SIGNATURE_ALGORITHM_PKCS1V15 = 'pkcs1v15';

    /** @var string */
    protected $key;

    /** @var string */
    protected $hashAlgorithm;

    /** @var string */
    protected $input;

    /** @var string */
    protected $signature_algorithm;

    /**
     * @param string $key
     * @param string $input
     * @param string $signature_algorithm
     */
    public function __construct($key, $hashAlgorithm, $input, $signature_algorithm = self::SIGNATURE_ALGORITHM_PSS)
    {
        $this->key = $key;
        $this->hashAlgorithm = $hashAlgorithm;
        $this->input = $input;
        $this->signature_algorithm = $signature_algorithm;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getHashAlgorithm()
    {
        return $this->hashAlgorithm;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return string
     */
    public function getSignatureAlgorithm()
    {
        return $this->signature_algorithm;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'input' => $this->input,
            'signature_algorithm' => $this->signature_algorithm
        ];
    }
}
