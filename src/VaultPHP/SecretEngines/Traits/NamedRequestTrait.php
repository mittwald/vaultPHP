<?php

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait NamedRequestTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait NamedRequestTrait
{
    /** @var string */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
