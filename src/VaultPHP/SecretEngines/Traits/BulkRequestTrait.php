<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Traits;

/**
 * Trait BulkRequestTrait.
 */
trait BulkRequestTrait
{
    protected array $batch_input = [];

    public function addBulkRequest(mixed $request): static
    {
        $this->batch_input[] = $request;

        return $this;
    }

    public function addBulkRequests(array $requests): void
    {
        foreach ($requests as $request) {
            $this->addBulkRequest($request);
        }
    }
}
