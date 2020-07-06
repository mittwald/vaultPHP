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
    private $vaultClient;

    /**
     * @param VaultClient $VaultClient
     * @return void
     */
    public function setVaultClient(VaultClient $VaultClient)
    {
        $this->vaultClient = $VaultClient;
    }

    /**
     * @return VaultClient
     * @throws VaultException
     */
    public function getVaultClient()
    {
        if (!$this->vaultClient) {
            throw new VaultException('Trying to request the VaultClient before initialization');
        }
        
        return $this->vaultClient;
    }
}
