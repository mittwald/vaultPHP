<?php

namespace Test\VaultPHP\Mocks;

use Psr\Http\Message\ResponseInterface;
use VaultPHP\Response\EndpointResponse;

/**
 * Class InvalidEndpointResponseMock
 * @package Test\VaultPHP
 */
class InvalidEndpointResponseMock extends EndpointResponse {

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public static function fromResponse(ResponseInterface $response)
    {
        return 'IamInvalid';
    }

    public function getMetaData()
    {
        return false;
    }

    public function hasErrors()
    {
        return true;
    }
}
