<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Traits;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;

/**
 * Trait ArrayExportTrait.
 */
trait ArrayExportTrait
{
    public function toArray(): array
    {
        $data = get_object_vars($this);
        $result = $this->array_map_r(
            /** @psalm-suppress MissingClosureParamType */
            function ($v) {
                if ($v instanceof ArrayExportInterface) {
                    return $v->toArray();
                }

                return $v;
            },
            $data
        );

        // only drop unset (null) values - false, 0 and '' are meaningful
        // for vault (e.g. deletion_allowed=false, min_encryption_version=0)
        return array_filter($result, static fn (mixed $value): bool => null !== $value);
    }

    private function array_map_r(callable $callback, array $input): array
    {
        $output = [];

        /**
         * @var string $key
         * @var mixed  $data
         */
        foreach ($input as $key => $data) {
            if (is_array($data)) {
                $output[$key] = $this->array_map_r($callback, $data);
            } else {
                // @psalm-suppress MixedAssignment
                $output[$key] = $callback($data);
            }
        }

        return $output;
    }
}
