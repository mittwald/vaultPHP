<?php

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
    public function toArray();
}
