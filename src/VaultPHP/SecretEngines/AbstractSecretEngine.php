<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines;

use VaultPHP\VaultClient;

/**
 * Class AbstractSecretEngine.
 */
abstract class AbstractSecretEngine
{
    protected VaultClient $vaultClient;

    public function __construct(VaultClient $VaultClient)
    {
        $this->vaultClient = $VaultClient;
    }
}
