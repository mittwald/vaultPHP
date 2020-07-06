<?php

namespace VaultPHP\Authentication;

use VaultPHP\Exceptions\VaultException;
use VaultPHP\VaultClient;

/**
 * Interface AuthenticationProviderInterface
 * @package VaultPHP\Authentication
 */
interface AuthenticationProviderInterface
{
    /**
     * @return AuthenticationMetaData|boolean
     */
    public function authenticate();

    /**
     * @param VaultClient $VaultClient
     * @return void
     */
    public function setVaultClient(VaultClient $VaultClient);

    /**
     * @return VaultClient
     * @throws VaultException
     */
    public function getVaultClient();
}
