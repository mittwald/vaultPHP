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
    protected $ciphertext;

    /** @var string|null */
    protected $context;

    /** @var string|null */
    protected $nonce;

    /**
     * DecryptData constructor.
     * @param string $ciphertext
     * @param string|null $context
     * @param string|null $nonce
     */
    public function __construct($ciphertext, $context = null, $nonce = null)
    {
        $this->setCiphertext($ciphertext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    /**
     * @return string
     */
    public function getCiphertext()
    {
        return $this->ciphertext;
    }

    /**
     * @param string $ciphertext
     * @return self
     */
    public function setCiphertext($ciphertext)
    {
        $this->ciphertext = $ciphertext;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string|null $nonce
     * @return self
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string|null $context
     * @return self
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }
}
