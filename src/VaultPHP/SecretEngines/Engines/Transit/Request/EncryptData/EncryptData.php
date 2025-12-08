<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;

/**
 * Class EncryptData.
 */
class EncryptData implements ArrayExportInterface
{
    use ArrayExportTrait;

    protected string $plaintext = '';

    protected ?string $context = null;

    protected ?string $nonce = null;

    /**
     * EncryptData constructor.
     */
    public function __construct(string $plaintext, ?string $context = null, ?string $nonce = null)
    {
        $this->setPlaintext($plaintext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    public function setPlaintext(string $plaintext): static
    {
        $this->plaintext = base64_encode($plaintext);

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

    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getPlaintext(): string
    {
        return $this->plaintext;
    }
}
