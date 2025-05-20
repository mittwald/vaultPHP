<?php

namespace Test\VaultPHP\Response;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use VaultPHP\Response\ApiErrors;
use VaultPHP\Response\MetaData;

/**
 * Class BasicMetaResponseTest
 * @package Test\VaultPHP\Response
 */
final class MetaDataResponseTest extends TestCase
{
    private function array_map_assoc(callable $callback, array $array): array
    {
        return array_map(function($key) use ($callback, $array){
            return $callback($key, $array[$key]);
        }, array_keys($array));
    }

    private function createTestData(): array
    {
        $reflectionClass = new ReflectionClass(MetaData::class);
        $classProperties = $reflectionClass->getProperties();
        
        $data = [];
        foreach ($classProperties as $classProperty) {
            $name = $classProperty->getName();
            $nameMD5 = md5($name);
            $type = $classProperty->getType();
            $typeName = $type->getName() ?? "";

            $data[$name] = match ($typeName) {
                'string' => $nameMD5,
                'int' => intval($nameMD5, 042),
                'bool' => false,
                'array' => [$nameMD5, $nameMD5],
                'object' => (object)[$nameMD5, $nameMD5],
                default => throw new \Error("unknown test data type {$type}"),
            };
        }

        return $data;
    }

    private function checkDtoData($testData, $basicMetaData)
    {
        $this->assertTrue($basicMetaData->hasErrors());
        $this->assertEquals($testData['errors'], $basicMetaData->getErrors());
        $this->assertEquals($testData['lease_duration'], $basicMetaData->getLeaseDuration());
        $this->assertEquals($testData['auth'], $basicMetaData->getAuth());
        $this->assertEquals($testData['lease_id'], $basicMetaData->getLeaseId());
        $this->assertEquals($testData['renewable'], $basicMetaData->getRenewable());
        $this->assertEquals($testData['request_id'], $basicMetaData->getRequestId());
        $this->assertEquals($testData['warnings'], $basicMetaData->getWarnings());
        $this->assertEquals($testData['wrap_info'], $basicMetaData->getWrapInfo());
    }

    public function testCanPopulateArrayDataToSelf()
    {
        $testData = $this->createTestData();
        $basicMetaData = new MetaData($testData);

        $this->checkDtoData($testData, $basicMetaData);
    }

    public function testCanPopulateObjectDataToSelf()
    {
        $testData = $this->createTestData();
        $basicMetaData = new MetaData((object)$testData);

        $this->checkDtoData($testData, $basicMetaData);
    }

    public function testCheckForErrors(): void
    {
        $error = ["nO eXiStiNg kEy nAMed FOOOBAR cOULd bE foUnD"];
        $basicMetaData = new MetaData(['errors' => $error]);

        $this->assertTrue($basicMetaData->hasErrors());
        $this->assertEquals($error, $basicMetaData->getErrors());
        $this->assertTrue($basicMetaData->containsError(ApiErrors::ENCRYPTION_KEY_NOT_FOUND));

        $basicMetaData = new MetaData(['errors' => []]);

        $this->assertFalse($basicMetaData->hasErrors());
        $this->assertEquals([], $basicMetaData->getErrors());
        $this->assertFalse($basicMetaData->containsError(ApiErrors::ENCRYPTION_KEY_NOT_FOUND));
    }
}
