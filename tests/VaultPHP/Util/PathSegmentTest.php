<?php
declare(strict_types=1);

namespace Test\VaultPHP\Util;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Util\PathSegment;

/**
 * Class PathSegmentTest
 * @package Test\VaultPHP\Util
 */
final class PathSegmentTest extends TestCase
{
    public static function encodedSegments(): array
    {
        return [
            'plain name' => ['my-key_1', 'my-key_1'],
            'dots inside name' => ['my.key', 'my.key'],
            'at sign' => ['user@example', 'user%40example'],
            'slash' => ['../../sys/policy', '..%2F..%2Fsys%2Fpolicy'],
            'query and fragment' => ['key?x=1#frag', 'key%3Fx%3D1%23frag'],
            'space' => ['my key', 'my%20key'],
            'percent' => ['%2e%2e', '%252e%252e'],
        ];
    }

    #[DataProvider('encodedSegments')]
    public function testEncode(string $segment, string $expected): void
    {
        $this->assertSame($expected, PathSegment::encode($segment));
    }

    public static function invalidSegments(): array
    {
        return [
            'empty' => [''],
            'current directory' => ['.'],
            'parent directory' => ['..'],
        ];
    }

    #[DataProvider('invalidSegments')]
    public function testInvalidSegmentIsRejected(string $segment): void
    {
        $this->expectException(VaultException::class);

        PathSegment::encode($segment);
    }
}
