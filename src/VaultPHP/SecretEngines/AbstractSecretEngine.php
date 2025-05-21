<?php
declare(strict_types=1);

namespace VaultPHP\SecretEngines;

use VaultPHP\VaultClient;

/**
 * Class AbstractSecretEngine
 * @package VaultPHP\SecretEngines
 */
abstract class AbstractSecretEngine
{
    /** @var VaultClient */
    protected VaultClient $vaultClient;

    /**
     * @param VaultClient $VaultClient
     */
    public function __construct(VaultClient $VaultClient)
    {
        $this->vaultClient = $VaultClient;
    }
}
