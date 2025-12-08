<?php

declare(strict_types=1);

namespace VaultPHP\SecretEngines\Traits;

trait PopulateDataTrait
{
    private function populateData(array|object $data): void
    {
        /** @var string $key */
        foreach ($data as $key => $value) {
            if (method_exists(static::class, "set{$this->toCamelCase($key)}")) {
                call_user_func([static::class, "set{$this->toCamelCase($key)}"], $value);
            } elseif (property_exists(static::class, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    private function toCamelCase(string $str): string
    {
        return lcfirst(
            str_replace(' ', '', ucwords(str_replace('_', ' ', $str)))
        );
    }
}
