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
    static function fromResponse(ResponseInterface $response);

    /**
     * @param ResponseInterface $response
     * @return BulkEndpointResponse
     */
    static function fromBulkResponse(ResponseInterface $response);


    /**
     * @return BasicMetaResponse
     */
    public function getBasicMetaResponse();

    /**
     * @return mixed
     */
    public function hasErrors();
}