<?php

namespace VaultPHP\Response;

use VaultPHP\SecretEngines\Traits\PopulateDataTrait;

/**
 * Class MetaData
 * @package VaultPHP\Response
 */
final class MetaData implements MetaDataInterface
{
    use PopulateDataTrait;

    /** @var string|null */
    private ?string $request_id = null;

    /** @var string|null */
    private ?string $lease_id = null;

    /** @var boolean */
    private bool $renewable = false;

    /** @var integer|null */
    private ?int $lease_duration = null;

    /** @var string|null */
    private ?string $wrap_info = null;

    /** @var string[] */
    private array $warnings = [];

    /** @var object|null */
    private ?object $auth = null;

    /** @var string[] */
    private array $errors = [];

    /**
     * MetaData constructor.
     * @param array|object $data
     */
    public function __construct(array|object $data = [])
    {
        $this->populateData($data);
    }

    /**
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->request_id;
    }

    /**
     * @return string|null
     */
    public function getLeaseId(): ?string
    {
        return $this->lease_id;
    }

    /**
     * @return bool|null
     */
    public function getRenewable(): ?bool
    {
        return $this->renewable;
    }

    /**
     * @return int|null
     */
    public function getLeaseDuration(): ?int
    {
        return $this->lease_duration;
    }

    /**
     * @param mixed $lease_duration
     * @return void
     */
    public function setLeaseDuration(mixed $lease_duration): void
    {
        $this->lease_duration = (int) $lease_duration;
    }


    /**
     * @return string|null
     */
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

    /**
     * @return object|null
     */
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

    /**
     * @param array $error
     * @return boolean
     */
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

    /**
     * @return boolean
     */
    #[\Override]
    public function hasErrors(): bool
    {
        $errors = $this->getErrors();
        return count($errors) >= 1;
    }
}
