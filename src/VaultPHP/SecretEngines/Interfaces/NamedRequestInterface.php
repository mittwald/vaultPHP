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
    public function getName(): string;

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name): static;
}
