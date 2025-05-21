<?php

namespace VaultPHP\SecretEngines\Interfaces;

use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;

/**
 * Interface EncryptionTypeRequestInterface
 * @package VaultPHP\SecretEngines\Interfaces
 */
interface EncryptionTypeRequestInterface
{
    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param EncryptionType $type
     * @return static
     */
    public function setType(EncryptionType $type): static;
}
