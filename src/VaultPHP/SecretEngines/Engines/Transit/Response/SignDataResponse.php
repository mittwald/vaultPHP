<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

final class SignDataResponse extends EndpointResponse
{
    /** @var string */
    protected $signature = '';

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }
}
