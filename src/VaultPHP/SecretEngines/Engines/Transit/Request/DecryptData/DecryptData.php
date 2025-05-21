<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;

/**
 * Class DecryptData
 * @package VaultPHP\SecretEngines\Transit\Request
 */
class DecryptData implements ArrayExportInterface
{
    use ArrayExportTrait;

    /** @var string */
    protected string $ciphertext = '';

    /** @var string|null */
    protected ?string $context = null;

    /** @var string|null */
    protected ?string $nonce = null;

    /**
     * DecryptData constructor.
     * @param string $ciphertext
     * @param string|null $context
     * @param string|null $nonce
     */
    public function __construct(string $ciphertext, string $context = null, string $nonce = null)
    {
        $this->setCiphertext($ciphertext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    /**
     * @param string $ciphertext
     * @return static
     */
    public function setCiphertext(string $ciphertext): static
    {
        $this->ciphertext = $ciphertext;
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
     * @return string
     */
    public function getCiphertext(): string
    {
        return $this->ciphertext;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @return string|null
     */
    public function getNonce(): ?string
    {
        return $this->nonce;
    }
}
