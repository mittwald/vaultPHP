<?php

namespace Test\VaultPHP\Mocks;

use Psr\Http\Message\ResponseInterface;
use VaultPHP\Response\EndpointResponseInterface;

/**
 * Class EndpointResponseMock
 * @package Test\VaultPHP
 */
class EndpointResponseMock implements EndpointResponseInterface {

    public static function fromResponse(ResponseInterface $response)
    {
        return 'IamInvalid';
    }

    public function getBasicMetaResponse()
    {
        return false;
    }
}
