<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class CreateKeyRequest
 * @package VaultPHP\SecretEngines\Transit\Request
 */
final class CreateKeyRequest implements ResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;

    /** @var string|null Encryption Type */
    protected $type;

    /**
     * CreateKeyRequest constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
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

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
}
