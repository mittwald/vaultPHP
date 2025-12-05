<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class ListKeysResponse.
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
