<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Engines\Transit\Request;

use VaultPHP\SecretEngines\Interfaces\NamedRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\SecretEngines\Traits\ArrayExportTrait;
use VaultPHP\SecretEngines\Traits\NamedRequestTrait;

/**
 * Class UpdateKeyConfigRequest.
 */
final class UpdateKeyConfigRequest implements ResourceRequestInterface, NamedRequestInterface
{
    use ArrayExportTrait;
    use NamedRequestTrait;

    protected ?int $min_decryption_version = null;

    protected ?int $min_encryption_version = null;

    protected ?bool $exportable = null;

    protected ?bool $allow_plaintext_backup = null;

    protected ?bool $deletion_allowed = null;

    public function getMinDecryptionVersion(): ?int
    {
        return $this->min_decryption_version;
    }

    public function setMinDecryptionVersion(?int $min_decryption_version): void
    {
        $this->min_decryption_version = $min_decryption_version;
    }

    public function getMinEncryptionVersion(): ?int
    {
        return $this->min_encryption_version;
    }

    public function setMinEncryptionVersion(?int $min_encryption_version): void
    {
        $this->min_encryption_version = $min_encryption_version;
    }

    public function getExportable(): ?bool
    {
        return $this->exportable;
    }

    public function setExportable(?bool $exportable): void
    {
        $this->exportable = $exportable;
    }

    public function getAllowPlaintextBackup(): ?bool
    {
        return $this->allow_plaintext_backup;
    }

    public function setAllowPlaintextBackup(?bool $allow_plaintext_backup): void
    {
        $this->allow_plaintext_backup = $allow_plaintext_backup;
    }

    public function getDeletionAllowed(): ?bool
    {
        return $this->deletion_allowed;
    }

    public function setDeletionAllowed(?bool $deletion_allowed): void
    {
        $this->deletion_allowed = $deletion_allowed;
    }
}
