<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class EncryptDataResponse
 * @package VaultPHP\SecretEngines\Transit\Response
 */
final class EncryptDataResponse extends EndpointResponse
{
    /** @var string */
    protected string $ciphertext = '';

    /**
     * @return string
     */
    public function getCiphertext(): string
    {
        return $this->ciphertext;
    }
}
