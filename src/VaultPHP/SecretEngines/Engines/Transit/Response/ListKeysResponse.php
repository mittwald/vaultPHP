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
    protected $keys = [];

    /**
     * @return mixed
     */
    public function getKeys()
    {
        return $this->keys;
    }
}
