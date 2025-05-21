<?php

namespace VaultPHP\Authentication;

use VaultPHP\Exceptions\VaultException;
use VaultPHP\VaultClient;

/**
 * Class AbstractAuthenticationProvider
 * @package VaultPHP\Authentication
 */
abstract class AbstractAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var VaultClient|null */
    private ?VaultClient $vaultClient = null;

    /**
     * @param VaultClient $VaultClient
     * @return void
     */
    #[\Override]
    public function setVaultClient(VaultClient $VaultClient): void
    {
        $this->vaultClient = $VaultClient;
    }

    /**
     * @throws VaultException
     * @return VaultClient
     */
    #[\Override]
    public function getVaultClient(): VaultClient
    {
        if (!$this->vaultClient) {
            throw new VaultException('Trying to request the VaultClient before initialization');
        }
        
        return $this->vaultClient;
    }
}
