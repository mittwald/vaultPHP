<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Interfaces;

/**
 * Interface NamedRequestInterface.
 */
interface NamedRequestInterface
{
    public function getName(): string;

    public function setName(string $name): static;
}
