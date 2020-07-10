<?php

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait EncryptionTypeRequestTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait EncryptionTypeRequestTrait
{
    /** @var string|null */
    protected $type;

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
