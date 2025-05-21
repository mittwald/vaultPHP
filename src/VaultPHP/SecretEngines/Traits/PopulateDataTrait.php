<?php

namespace VaultPHP\SecretEngines\Traits;

trait PopulateDataTrait
{
    /**
     * @param object|array $data
     * @return void
     */
    private function populateData(object|array $data): void
    {
        /** @var string $key */
        foreach ($data as $key => $value) {
            if(method_exists(static::class, "set{$this->toCamelCase($key)}")) {
                call_user_func([static::class, "set{$this->toCamelCase($key)}"], $value);
            } else if (property_exists(static::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @param string $str
     * @return string
     */
    private function toCamelCase (string $str): string {
        return lcfirst(
            str_replace(' ', '', ucwords(str_replace('_', ' ', $str)))
        );
    }
}