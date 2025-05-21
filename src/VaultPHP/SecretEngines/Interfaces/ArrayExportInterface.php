<?php
declare(strict_types=1);

namespace VaultPHP\SecretEngines\Interfaces;

/**
 * Interface ArrayExportInterface
 * @package VaultPHP\SecretEngines\Traits
 */
interface ArrayExportInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}
