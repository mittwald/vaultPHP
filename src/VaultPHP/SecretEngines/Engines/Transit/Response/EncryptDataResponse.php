<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class EncryptDataResponse.
 */
final class EncryptDataResponse extends EndpointResponse
{
    protected string $ciphertext = '';

    public function getCiphertext(): string
    {
        return $this->ciphertext;
    }
}
