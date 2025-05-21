<?php
declare(strict_types=1);

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait NamedRequestTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait NamedRequestTrait
{
    /** @var string */
    protected string $name = "";

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
