<?php

namespace Test\VaultPHP\Mocks;

use Psr\Http\Message\ResponseInterface;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\Response\MetaData;

class InvalidStaticClass
{
    const someAction = "invalid";

    public static function someAction() {
    }
}

/**
 * Class InvalidEndpointResponseMock
 * @package Test\VaultPHP
 */
class InvalidEndpointResponseMock extends EndpointResponse {

    #[\Override]
    public function getMetaData(): \VaultPHP\Response\MetaData
    {
        return new MetaData();
    }

    #[\Override]
    public function hasErrors(): bool
    {
        return true;
    }
}
