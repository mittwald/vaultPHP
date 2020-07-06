<?php

namespace VaultPHP\SecretEngines;

use VaultPHP\VaultClient;

/**
 * Class AbstractSecretEngine
 * @package VaultPHP\SecretEngines
 */
abstract class AbstractSecretEngine
{
    /** @var VaultClient */
    protected $vaultClient;

    /**
     * @param VaultClient $VaultClient
     */
    public function __construct(VaultClient $VaultClient)
    {
        $this->vaultClient = $VaultClient;
    }
}
