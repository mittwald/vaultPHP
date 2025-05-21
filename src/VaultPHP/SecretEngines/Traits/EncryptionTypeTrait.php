<?php

namespace VaultPHP\SecretEngines\Traits;

use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;

trait EncryptionTypeTrait
{
    protected ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(EncryptionType $type): static
    {
        $this->type = $type->value;
        return $this;
    }
}
