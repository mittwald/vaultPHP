<?php
declare(strict_types=1);

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
    protected ?int $min_decryption_version = null;

    /** @var int|null */
    protected ?int $min_encryption_version = null;

    /** @var boolean|null */
    protected ?bool $exportable = null;

    /** @var boolean|null */
    protected ?bool $allow_plaintext_backup = null;

    /** @var boolean|null */
    protected ?bool $deletion_allowed = null;

    /**
     * @return int|null
     */
    public function getMinDecryptionVersion(): ?int
    {
        return $this->min_decryption_version;
    }

    /**
     * @param int|null $min_decryption_version
     * @return void
     */
    public function setMinDecryptionVersion(?int $min_decryption_version): void
    {
        $this->min_decryption_version = $min_decryption_version;
    }

    /**
     * @return int|null
     */
    public function getMinEncryptionVersion(): ?int
    {
        return $this->min_encryption_version;
    }

    /**
     * @param int|null $min_encryption_version
     * @return void
     */
    public function setMinEncryptionVersion(?int $min_encryption_version): void
    {
        $this->min_encryption_version = $min_encryption_version;
    }

    /**
     * @return bool|null
     */
    public function getExportable(): ?bool
    {
        return $this->exportable;
    }

    /**
     * @param bool|null $exportable
     * @return void
     */
    public function setExportable(?bool $exportable): void
    {
        $this->exportable = $exportable;
    }

    /**
     * @return bool|null
     */
    public function getAllowPlaintextBackup(): ?bool
    {
        return $this->allow_plaintext_backup;
    }

    /**
     * @param bool|null $allow_plaintext_backup
     * @return void
     */
    public function setAllowPlaintextBackup(?bool $allow_plaintext_backup): void
    {
        $this->allow_plaintext_backup = $allow_plaintext_backup;
    }

    /**
     * @return bool|null
     */
    public function getDeletionAllowed(): ?bool
    {
        return $this->deletion_allowed;
    }

    /**
     * @param bool|null $deletion_allowed
     * @return void
     */
    public function setDeletionAllowed(?bool $deletion_allowed): void
    {
        $this->deletion_allowed = $deletion_allowed;
    }
}
