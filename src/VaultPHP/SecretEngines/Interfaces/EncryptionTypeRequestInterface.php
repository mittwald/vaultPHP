<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Interfaces;

use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;

/**
 * Interface EncryptionTypeRequestInterface.
 */
interface EncryptionTypeRequestInterface
{
    public function getType(): ?string;

    public function setType(EncryptionType $type): static;
}
