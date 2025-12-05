<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class DecryptDataResponse.
 */
final class DecryptDataResponse extends EndpointResponse
{
    protected string $plaintext = '';

    public function getPlaintext(): string
    {
        return base64_decode($this->plaintext);
    }
}
