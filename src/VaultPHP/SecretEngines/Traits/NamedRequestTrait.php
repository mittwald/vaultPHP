<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait NamedRequestTrait.
 */
trait NamedRequestTrait
{
    protected string $name = '';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
