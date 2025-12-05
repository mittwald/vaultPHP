<?php

declare(strict_types=1);

namespace VaultPHP\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface EndpointResponseInterface.
 */
interface EndpointResponseInterface
{
    public static function fromResponse(ResponseInterface $response): static;

    public function getMetaData(): MetaData;
}
