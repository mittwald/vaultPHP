<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData;

use VaultPHP\SecretEngines\Interfaces\EncryptionTypeRequestInterface;
use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\EncryptionTypeTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class EncryptDataRequest.
 */
final class EncryptDataRequest extends EncryptData implements ResourceRequestInterface, NamedRequestInterface, EncryptionTypeRequestInterface
{
    use NamedRequestTrait;
    use EncryptionTypeTrait;

    /**
     * EncryptDataRequest constructor.
     */
    public function __construct(string $name, string $plaintext)
    {
        parent::__construct($plaintext);
        $this->setName($name);
    }
}
