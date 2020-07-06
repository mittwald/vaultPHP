<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData;

use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class DecryptDataRequest
 * @package VaultPHP\SecretEngines\Transit\Request
 */
final class DecryptDataRequest extends DecryptData implements ResourceRequestInterface, NamedRequestInterface
{
    use NamedRequestTrait;

    /**
     * DecryptDataRequest constructor.
     * @param string $name
     * @param string $ciphertext
     */
    public function __construct($name, $ciphertext)
    {
        parent::__construct($ciphertext);
        $this->setName($name);
    }
}
