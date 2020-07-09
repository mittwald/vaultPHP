<?php

namespace VaultPHP\SecretEngines\Traits;

use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;

/**
 * Trait ArrayExportTrait
 * @package VaultPHP\SecretEngines\Traits
 */
trait ArrayExportTrait
{
    /**
     * @param callable $callback
     * @param array $input
     * @return array
     */
    private function array_map_r($callback, $input)
    {
        $output = [];

        /**
         * @var string $key
         * @var mixed $data
         */
        foreach ($input as $key => $data) {
            if (is_array($data)) {
                $output[$key] = $this->array_map_r($callback, $data);
            } else {
                /** @psalm-suppress MixedAssignment */
                $output[$key] = $callback($data);
            }
        }

        return $output;
    }

    /**
     * @return array
     */
    public function toArray()
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

        return array_filter($result);
    }
}
