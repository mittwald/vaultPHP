<?php

namespace VaultPHP\Response;

use ArrayAccess;
use Countable;
use Iterator;
use VaultPHP\Exceptions\VaultException;

/**
 * Class BulkEndpointResponse
 *
 * @template-implements Iterator<int>
 * @template-implements ArrayAccess<int, static>
 */
final class BulkEndpointResponse extends EndpointResponse implements Iterator, ArrayAccess, Countable
{
    /** @var integer */
    private int $iteratorPosition = 0;

    protected array $batch_results = [];

    #[\Override]
    public function hasErrors(): bool
    {
        $errorOccurred = $this->getMetaData()->hasErrors();
        if (!$errorOccurred) {
            /** @var EndpointResponse $batchResult */
            foreach ($this as $batchResult) {
                $errorOccurred = $batchResult->getMetaData()->hasErrors();
                if ($errorOccurred) {
                    return true;
                }
            }
        }

        return $errorOccurred;
    }

    #[\Override]
    public function current(): mixed
    {
        return $this->batch_results[$this->iteratorPosition];
    }

    #[\Override]
    public function next(): void
    {
        ++$this->iteratorPosition;
    }

    #[\Override]
    public function key(): int
    {
        return $this->iteratorPosition;
    }

    #[\Override]
    public function valid(): bool
    {
        return isset($this->batch_results[$this->iteratorPosition]);
    }

    #[\Override]
    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    #[\Override]
    public function offsetExists($offset): bool
    {
        return isset($this->batch_results[$offset]);
    }

    #[\Override]
    public function offsetGet($offset): mixed
    {
        return $this->batch_results[$offset];
    }

    /**
     * @throws VaultException
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new VaultException('readonly');
    }

    /**
     * @throws VaultException
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new VaultException('readonly');
    }

    #[\Override]
    public function count(): int
    {
        return count($this->batch_results);
    }
}
