<?php
declare(strict_types=1);

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
    public function __construct(string $name, string $ciphertext)
    {
        parent::__construct($ciphertext);
        $this->setName($name);
    }
}
