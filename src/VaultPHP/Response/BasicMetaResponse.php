<?php

namespace VaultPHP\Response;

/**
 * Class BasicMetaResponse
 * @package VaultPHP\Response
 */
class BasicMetaResponse implements BasicMetaResponseInterface
{
    /** @var string|null */
    private $request_id;

    /** @var string|null */
    private $lease_id;

    /** @var boolean|null */
    private $renewable;

    /** @var integer|null */
    private $lease_duration;

    /** @var string|null */
    private $wrap_info;

    /** @var array|null */
    private $warnings;

    /** @var object|null */
    private $auth;

    /** @var array|null */
    private $errors;

    /**
     * BasicMetaResponse constructor.
     * @param array|object $data
     */
    public function __construct($data = [])
    {
        $this->populateData($data);
    }

    /**
     * @param array|object $data
     * @return void
     */
    private function populateData($data)
    {
        /** @var string $key */
        /** @var mixed $value */
        foreach ($data as $key => $value) {
            if (property_exists(self::class, (string) $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return string|null
     */
    public function getRequestId()
    {
        return $this->request_id;
    }

    /**
     * @return string|null
     */
    public function getLeaseId()
    {
        return $this->lease_id;
    }

    /**
     * @return bool|null
     */
    public function getRenewable()
    {
        return $this->renewable;
    }

    /**
     * @return int|null
     */
    public function getLeaseDuration()
    {
        return $this->lease_duration;
    }

    /**
     * @return string|null
     */
    public function getWrapInfo()
    {
        return $this->wrap_info;
    }

    /**
     * @return array|null
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return object|null
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @return array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        $errors = $this->getErrors();
        return $errors !== NULL && is_array($errors) && count($errors) >= 1;
    }
}
