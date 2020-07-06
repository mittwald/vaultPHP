<?php

namespace VaultPHP\SecretEngines\Interfaces;

/**
 * Interface NamedRequestInterface
 * @package VaultPHP\SecretEngines\Interfaces
 */
interface NamedRequestInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return void
     */
    public function setName($name);
}
