<?php

namespace VaultPHP\Response;

/**
 * Interface MetaDataInterface
 * @package VaultPHP\Response
 */
interface MetaDataInterface
{
    /**
     * GenericEndpointResponse constructor.
     * @param array $data
     */
    public function __construct(array $data);

    /**
     * @return object|null
     */
    public function getAuth(): ?object;

    /**
     * @return array|null
     */
    public function getErrors(): ?array;

    /**
     * @return boolean
     */
    public function hasErrors(): bool;

    /**
     * @param array $error
     * @return bool
     */
    public function containsError(array $error): bool;
}
