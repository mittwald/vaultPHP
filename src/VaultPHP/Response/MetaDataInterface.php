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
    public function __construct($data);

    /**
     * @return string|null
     */
    public function getRequestId();

    /**
     * @return string|null
     */
    public function getLeaseId();

    /**
     * @return boolean|null
     */
    public function getRenewable();

    /**
     * @return int|null
     */
    public function getLeaseDuration();

    /**
     * @return string|null
     */
    public function getWrapInfo();

    /**
     * @return array|null
     */
    public function getWarnings();

    /**
     * @return object|null
     */
    public function getAuth();

    /**
     * @return array|null
     */
    public function getErrors();

    /**
     * @return boolean
     */
    public function hasErrors();

    /**
     * @param array $error
     * @return bool
     */
    public function containsError($error);
}
