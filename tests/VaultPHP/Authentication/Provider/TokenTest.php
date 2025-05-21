<?php
declare(strict_types=1);

namespace Test\VaultPHP\Authentication\Provider;

use PHPUnit\Framework\TestCase;
use VaultPHP\Authentication\Provider\Token;

/**
 * Class TokenTest
 * @package Test\VaultPHP\Authentication\Provider
 */
final class TokenTest extends TestCase
{
    public function testGetToken(): void
    {
        $tokenAuth = new Token('foobar');
        $tokenMeta = $tokenAuth->authenticate();

        $this->assertEquals('foobar', $tokenMeta->getClientToken());
    }
}
