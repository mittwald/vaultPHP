<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData;

use VaultPHP\SecretEngines\Interfaces\BulkResourceRequestInterface;
use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\BulkRequestTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class DecryptDataBulkRequest.
 */
final class DecryptDataBulkRequest implements BulkResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;
    use BulkRequestTrait;

    /**
     * DecryptDataRequest constructor.
     *
     * @param DecryptData[] $batchRequests
     */
    public function __construct(string $name, $batchRequests = [])
    {
        $this->setName($name);
        $this->addBulkRequests($batchRequests);
    }
}
