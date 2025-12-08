<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

final class SignDataResponse extends EndpointResponse
{
    protected string $signature = '';

    public function getSignature(): string
    {
        return $this->signature;
    }
}
