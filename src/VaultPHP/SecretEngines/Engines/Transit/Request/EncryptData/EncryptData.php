<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;

/**
 * Class EncryptData
 * @package VaultPHP\SecretEngines\Transit\Request\EncryptData
 */
class EncryptData implements ArrayExportInterface
{
    use ArrayExportTrait;

    /** @var string */
    protected string $plaintext = '';

    /** @var string|null */
    protected ?string $context = null;

    /** @var string|null */
    protected ?string $nonce = null;

    /**
     * EncryptData constructor.
     * @param string $plaintext
     * @param string|null $context
     * @param string|null $nonce
     */
    public function __construct(string $plaintext, string $context = null, string $nonce = null)
    {
        $this->setPlaintext($plaintext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    /**
     * @param string $plaintext
     * @return static
     */
    public function setPlaintext(string $plaintext): static
    {
        $this->plaintext = base64_encode($plaintext);
        return $this;
    }

    /**
     * @param string|null $nonce
     * @return static
     */
    public function setNonce(?string $nonce): static
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @param string|null $context
     * @return static
     */
    public function setContext(?string $context): static
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getPlaintext(): string
    {
        return $this->plaintext;
    }
}
