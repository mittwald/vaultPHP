<?php

declare(strict_types=1);

namespace VaultPHP\Util;

use VaultPHP\Exceptions\VaultException;

/**
 * Encodes a single value (e.g. a key name) that is interpolated into a Vault API path.
 *
 * Every caller supplied value that becomes part of a request path has to pass through
 * this helper, so it can't change the requested endpoint:
 *
 * - "/" is rejected: Vault decodes "%2F" back to "/" before routing, so an encoded
 *   slash still reaches another endpoint (e.g. "name/rotate" or "../../sys/...").
 *   Vault key, role and user names never contain a slash.
 * - the dot segments "." and ".." (which HTTP clients collapse before sending the
 *   request) as well as empty segments are rejected.
 * - everything else (e.g. "?" and "#") is percent-encoded.
 */
final class PathSegment
{
    /**
     * @throws VaultException
     */
    public static function encode(string $segment): string
    {
        if ('' === $segment || '.' === $segment || '..' === $segment || str_contains($segment, '/')) {
            throw new VaultException('Invalid path segment: must not be empty, "." or ".." or contain "/"');
        }

        return rawurlencode($segment);
    }
}
