<?php

declare(strict_types=1);

namespace VaultPHP\Util;

use VaultPHP\Exceptions\VaultException;

/**
 * Encodes a single value (e.g. a key name) that is interpolated into a Vault API path.
 *
 * Every caller supplied value that becomes part of a request path has to pass through
 * this helper, so it can't change the requested endpoint: "/", "?" and "#" are
 * percent-encoded and the dot segments "." and ".." (which HTTP clients collapse
 * before sending the request) as well as empty segments are rejected.
 */
final class PathSegment
{
    /**
     * @throws VaultException
     */
    public static function encode(string $segment): string
    {
        if ('' === $segment || '.' === $segment || '..' === $segment) {
            throw new VaultException('Invalid path segment: must not be empty, "." or ".."');
        }

        return rawurlencode($segment);
    }
}
