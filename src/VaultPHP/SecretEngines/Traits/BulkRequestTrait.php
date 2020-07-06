<?php

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait BulkRequestTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait BulkRequestTrait
{
    /** @var mixed[] */
    protected $batch_input = [];

    /**
     * @param mixed $request
     * @return void
     */
    public function addBulkRequest($request)
    {
        $this->batch_input[] = $request;
    }

    /**
     * @param mixed[] $requests
     * @return void
     */
    public function addBulkRequests($requests)
    {
        /** @var mixed $request */
        foreach ($requests as $request) {
            $this->addBulkRequest($request);
        }
    }
}
