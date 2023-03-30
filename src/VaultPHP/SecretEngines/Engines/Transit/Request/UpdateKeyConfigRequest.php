<?php

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class UpdateKeyConfigRequest
 * @package VaultPHP\SecretEngines\Transit\Request
 */
final class UpdateKeyConfigRequest implements ResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;

    /** @var int|null */
    protected $min_decryption_version;

    /** @var int|null */
    protected $min_encryption_version;

    /** @var boolean|null */
    protected $exportable;

    /** @var boolean|null */
    protected $allow_plaintext_backup;

    /** @var boolean|null */
    protected $deletion_allowed;

    /**
     * UpdateKeyConfigRequest constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param $allow boolean
     * @return $this
     */
    public function setDeletionAllowed($allow)
    {
        $this->deletion_allowed = (boolean)$allow;
        return $this;
    }

    /**
     * @param int $min_decryption_version
     * @return $this
     */
    public function setMinDecryptionVersion($min_decryption_version)
    {
        $this->min_decryption_version = $min_decryption_version;
        return $this;
    }

    /**
     * @param int $min_encryption_version
     * @return $this
     */
    public function setMinEncryptionVersion($min_encryption_version)
    {
        $this->min_encryption_version = $min_encryption_version;
        return $this;
    }

    /**
     * @param bool $exportable
     * @return $this
     */
    public function setExportable($exportable)
    {
        $this->exportable = $exportable;
        return $this;
    }

    /**
     * @param bool $allow_plaintext_backup
     * @return $this
     */
    public function setAllowPlaintextBackup($allow_plaintext_backup)
    {
        $this->allow_plaintext_backup = $allow_plaintext_backup;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinDecryptionVersion()
    {
        return $this->min_decryption_version;
    }

    /**
     * @return int|null
     */
    public function getMinEncryptionVersion()
    {
        return $this->min_encryption_version;
    }

    /**
     * @return bool|null
     */
    public function getExportable()
    {
        return $this->exportable;
    }

    /**
     * @return bool|null
     */
    public function getAllowPlaintextBackup()
    {
        return $this->allow_plaintext_backup;
    }

    /**
     * @return bool|null
     */
    public function getDeletionAllowed()
    {
        return $this->deletion_allowed;
    }
}
