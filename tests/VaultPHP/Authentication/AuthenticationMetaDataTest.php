<?php

namespace Test\VaultPHP\Authentication;

use PHPUnit\Framework\TestCase;
use VaultPHP\Authentication\AuthenticationMetaData;

/**
 * Class AuthenticationMetaDataTest
 * @package Test\VaultPHP\Authentication
 */
final class AuthenticationMetaDataTest extends TestCase
{
    public function testExtractFromAuthObject()
    {
        $testStd = new \stdClass();
        $testStd->client_token = "foobar";

        $meta = new AuthenticationMetaData($testStd);
        $this->assertEquals($testStd->client_token, $meta->getClientToken());
    }
}
