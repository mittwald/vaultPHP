<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Interfaces;

/**
 * Interface ArrayExportInterface.
 */
interface ArrayExportInterface
{
    public function toArray(): array;
}
