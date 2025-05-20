<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\EncryptionTypeTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class CreateKeyRequest
 * @package VaultPHP\SecretEngines\Transit\Request
 */
final class CreateKeyRequest implements ResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;
    use EncryptionTypeTrait;
}
