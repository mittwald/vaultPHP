<?php

declare(strict_types=1);

namespace VaultPHP\Response;

/**
 * Interface MetaDataInterface.
 */
interface MetaDataInterface
{
    /**
     * GenericEndpointResponse constructor.
     */
    public function __construct(array $data);

    public function getAuth(): ?object;

    public function getErrors(): ?array;

    public function hasErrors(): bool;

    public function containsError(array $error): bool;
}
