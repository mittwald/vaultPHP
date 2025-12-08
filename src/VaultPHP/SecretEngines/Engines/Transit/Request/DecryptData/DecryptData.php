<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;

/**
 * Class DecryptData.
 */
class DecryptData implements ArrayExportInterface
{
    use ArrayExportTrait;

    protected string $ciphertext = '';

    protected ?string $context = null;

    protected ?string $nonce = null;

    /**
     * DecryptData constructor.
     */
    public function __construct(string $ciphertext, ?string $context = null, ?string $nonce = null)
    {
        $this->setCiphertext($ciphertext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    public function setCiphertext(string $ciphertext): static
    {
        $this->ciphertext = $ciphertext;

        return $this;
    }

    public function setNonce(?string $nonce): static
    {
        $this->nonce = $nonce;

        return $this;
    }

    public function setContext(?string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getCiphertext(): string
    {
        return $this->ciphertext;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getNonce(): ?string
    {
        return $this->nonce;
    }
}
