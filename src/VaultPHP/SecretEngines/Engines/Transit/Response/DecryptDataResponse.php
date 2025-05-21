<?php
declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Response;

use VaultPHP\Response\EndpointResponse;

/**
 * Class DecryptDataResponse
 * @package VaultPHP\SecretEngines\Transit\Response
 */
final class DecryptDataResponse extends EndpointResponse
{
    /** @var string */
    protected string $plaintext = '';

    /**
     * @return string
     */
    public function getPlaintext(): string
    {
        return base64_decode($this->plaintext);
    }
}
