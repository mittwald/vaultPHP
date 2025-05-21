<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData;

use VaultPHP\SecretEngines\Interfaces\BulkResourceRequestInterface;
use VaultPHP\SecretEngines\Interfaces\EncryptionTypeRequestInterface;
use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\BulkRequestTrait;
use VaultPHP\SecretEngines\Traits\EncryptionTypeTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

final class EncryptDataBulkRequest implements
    BulkResourceRequestInterface,
    NamedRequestInterface,
    EncryptionTypeRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;
    use BulkRequestTrait;
    use EncryptionTypeTrait;

    /**
     * EncryptDataRequest constructor.
     * @param string $name
     * @param EncryptData[] $batchRequests
     */
    public function __construct(string $name, $batchRequests = [])
    {
        $this->setName($name);
        $this->addBulkRequests($batchRequests);
    }
}
