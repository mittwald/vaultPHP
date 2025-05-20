<?php

namespace VaultPHP\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface EndpointResponseInterface
 * @package VaultPHP\Response
 */
interface EndpointResponseInterface
{
    /**
     * @param ResponseInterface $response
     * @return static
     */
    static function fromResponse(ResponseInterface $response): static;


    /**
     * @return MetaData
     */
    public function getMetaData(): MetaData;
}
