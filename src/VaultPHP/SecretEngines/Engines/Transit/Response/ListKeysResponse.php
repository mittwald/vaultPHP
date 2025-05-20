<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class ListKeysResponse
 * @package VaultPHP\SecretEngines\Transit\Response
 */
final class ListKeysResponse extends EndpointResponse
{
    /** @var string[] */
    protected array $keys = [];

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }
}
