<?php

namespace Test\VaultPHP\Response;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use VaultPHP\Response\BasicMetaResponse;

/**
 * Class BasicMetaResponseTest
 * @package Test\VaultPHP\Response
 */
final class BasicMetaResponseTest extends TestCase
{
    private function createTestData()
    {
        $reflectionClass = new ReflectionClass(BasicMetaResponse::class);

        $classPropertyNames = array_map(function ($property) {
            return $property->getName();
        }, $reflectionClass->getProperties());

        return array_combine(
            $classPropertyNames,
            array_map('md5', $classPropertyNames)
        );
    }

    private function checkDtoData($testData, $basicMetaData)
    {
        $this->assertEquals($testData['errors'], $basicMetaData->getErrors());
        $this->assertEquals(false, $basicMetaData->hasErrors());
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
        $basicMetaData = new BasicMetaResponse((array)$testData);
        $this->checkDtoData($testData, $basicMetaData);
    }

    public function testCanPopulateObjectDataToSelf()
    {
        $testData = $this->createTestData();
        $basicMetaData = new BasicMetaResponse((object)$testData);
        $this->checkDtoData($testData, $basicMetaData);
    }

    public function testCheckForErrors()
    {
        $error = ["foo"];
        $basicMetaData = new BasicMetaResponse(['errors' => $error]);

        $this->assertTrue($basicMetaData->hasErrors());
        $this->assertEquals($error, $basicMetaData->getErrors());

        $basicMetaData = new BasicMetaResponse(['errors' => []]);

        $this->assertFalse($basicMetaData->hasErrors());
        $this->assertEquals([], $basicMetaData->getErrors());
    }
}
