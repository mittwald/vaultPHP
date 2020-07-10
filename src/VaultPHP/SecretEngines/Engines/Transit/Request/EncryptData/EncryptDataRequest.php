<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData;

use VaultPHP\SecretEngines\Interfaces\EncryptionTypeRequestInterface;
use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\EncryptionTypeRequestTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class EncryptDataRequest
 * @package VaultPHP\SecretEngines\Transit\Request\EncryptData
 */
final class EncryptDataRequest extends EncryptData implements
    ResourceRequestInterface,
    NamedRequestInterface,
    EncryptionTypeRequestInterface
{
    use NamedRequestTrait;
    use EncryptionTypeRequestTrait;

    /**
     * EncryptDataRequest constructor.
     * @param string $name
     * @param string $plaintext
     */
    public function __construct($name, $plaintext)
    {
        parent::__construct($plaintext);
        $this->setName($name);
    }
}
