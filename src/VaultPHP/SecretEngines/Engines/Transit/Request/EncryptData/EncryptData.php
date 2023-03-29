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
    protected $plaintext;

    /** @var string|null */
    protected $context;

    /** @var string|null */
    protected $nonce;

    /**
     * EncryptData constructor.
     * @param string $plaintext
     * @param null|string $context
     * @param null|string $nonce
     */
    public function __construct($plaintext, $context = null, $nonce = null)
    {
        $this->setPlaintext($plaintext);
        $this->setContext($context);
        $this->setNonce($nonce);
    }

    /**
     * @return string
     */
    public function getPlaintext()
    {
        return $this->plaintext;
    }

    /**
     * @param string $plaintext
     * @return self
     */
    public function setPlaintext($plaintext)
    {
        $this->plaintext = base64_encode($plaintext);
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
