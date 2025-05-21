<?php

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait BulkRequestTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait BulkRequestTrait
{
    /** @var array */
    protected array $batch_input = [];

    /**
     * @param mixed $request
     * @return static
     */
    public function addBulkRequest(mixed $request): static
    {
        $this->batch_input[] = $request;
        return $this;
    }

    /**
     * @param array $requests
     * @return void
     */
    public function addBulkRequests(array $requests): void
    {
        foreach ($requests as $request) {
            $this->addBulkRequest($request);
        }
    }
}
