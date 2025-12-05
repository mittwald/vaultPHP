<?php

declare(strict_types=1);

namespace VaultPHP\Response;

use VaultPHP\SecretEngines\Traits\PopulateDataTrait;

/**
 * Class MetaData.
 */
final class MetaData implements MetaDataInterface
{
    use PopulateDataTrait;

    private ?string $request_id = null;

    private ?string $lease_id = null;

    private bool $renewable = false;

    private ?int $lease_duration = null;

    private ?string $wrap_info = null;

    /** @var string[] */
    private array $warnings = [];

    private ?object $auth = null;

    /** @var string[] */
    private array $errors = [];

    /**
     * MetaData constructor.
     */
    public function __construct(array|object $data = [])
    {
        $this->populateData($data);
    }

    public function getRequestId(): ?string
    {
        return $this->request_id;
    }

    public function getLeaseId(): ?string
    {
        return $this->lease_id;
    }

    public function getRenewable(): ?bool
    {
        return $this->renewable;
    }

    public function getLeaseDuration(): ?int
    {
        return $this->lease_duration;
    }

    public function setLeaseDuration(mixed $lease_duration): void
    {
        $this->lease_duration = (int) $lease_duration;
    }

    public function setWarnings(?array $warnings): void
    {
        $this->warnings = $warnings ?? [];
    }

    public function getWrapInfo(): ?string
    {
        return $this->wrap_info;
    }

    /**
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    #[\Override]
    public function getAuth(): ?object
    {
        return $this->auth;
    }

    /**
     * @return string[]
     */
    #[\Override]
    public function getErrors(): array
    {
        return $this->errors;
    }

    #[\Override]
    public function containsError(array $error): bool
    {
        foreach ($this->getErrors() as $apiError) {
            /** @var string $errorMessage */
            foreach ($error as $errorMessage) {
                if (preg_match("#{$errorMessage}#i", $apiError)) {
                    return true;
                }
            }
        }

        return false;
    }

    #[\Override]
    public function hasErrors(): bool
    {
        $errors = $this->getErrors();

        return count($errors) >= 1;
    }
}
