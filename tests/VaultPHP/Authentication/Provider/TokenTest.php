<?php

namespace Test\VaultPHP\Authentication\Provider;

use PHPUnit\Framework\TestCase;
use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Authentication\AuthenticationMetaData;

/**
 * Class TokenTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class TokenTest extends TestCase
{
    public function testGetToken()
    {
        $tokenAuth = new Token('foobar');
        $tokenMeta = $tokenAuth->authenticate();

        $this->assertInstanceOf(AuthenticationMetaData::class, $tokenMeta);
        $this->assertEquals('foobar', $tokenMeta->getClientToken());
    }
}
