<?php

declare(strict_types=1);

namespace VaultPHP\Authentication;

use VaultPHP\VaultClient;

/**
 * Interface AuthenticationProviderInterface.
 */
interface AuthenticationProviderInterface
{
    public function authenticate(): AuthenticationMetaData|bool;

    public function setVaultClient(VaultClient $VaultClient): void;
}
