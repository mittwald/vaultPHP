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
     * @return mixed
     */
    static function fromResponse(ResponseInterface $response);

    /**
     * @return BasicMetaResponse
     */
    public function getBasicMetaResponse();
}
