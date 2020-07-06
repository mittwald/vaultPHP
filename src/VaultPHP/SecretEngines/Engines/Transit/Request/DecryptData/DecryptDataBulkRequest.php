<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData;

use VaultPHP\SecretEngines\Interfaces\BulkResourceRequestInterface;
use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\BulkRequestTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class DecryptDataBulkRequest
 * @package VaultPHP\SecretEngines\Transit\Request
 */
final class DecryptDataBulkRequest implements BulkResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;
    use BulkRequestTrait;

    /**
     * DecryptDataRequest constructor.
     * @param string $name
     * @param DecryptData[] $batchRequests
     */
    public function __construct($name, $batchRequests = [])
    {
        $this->setName($name);
        $this->addBulkRequests($batchRequests);
    }

}
