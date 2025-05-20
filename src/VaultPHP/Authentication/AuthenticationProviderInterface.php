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
    public function authenticate(): AuthenticationMetaData|bool;

    /**
     * @param VaultClient $VaultClient
     * @return void
     */
    public function setVaultClient(VaultClient $VaultClient): void;

    /**
     * @return VaultClient
     * @throws VaultException
     */
    public function getVaultClient(): VaultClient;
}
