<?php
declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

final class SignDataResponse extends EndpointResponse
{
    /** @var string */
    protected string $signature = '';

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->signature;
    }
}
