<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class DecryptDataResponse
 * @package VaultPHP\SecretEngines\Transit\Response
 */
final class DecryptDataResponse extends EndpointResponse
{
    /** @var string */
    protected $plaintext = '';

    /**
     * @return string
     */
    public function getPlaintext()
    {
        return base64_decode($this->plaintext);
    }
}
