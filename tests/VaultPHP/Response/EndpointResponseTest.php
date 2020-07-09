<?php

namespace Test\VaultPHP\Response;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VaultPHP\Response\MetaData;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\DecryptDataResponse;

/**
 * Class EndpointResponseTest
 * @package Test\VaultPHP\Response
 */
final class EndpointResponseTest extends TestCase
{
    public function testCanGetPopulateMetaDataFromResponse()
    {
        $testMeta = [
            'request_id' => 1337,
            'lease_id' => 1338,
            'renewable' => true,
            'lease_duration' => 1339,
            'wrap_info' => 'foo',
            'warnings' => [
                'fooWarning',
                'fooWarning2',
            ],
            'auth' => [
                'token' => 'fooToken',
            ],
            'errors' => [
                'metaDataError',
                'metaDataError2',
            ],
        ];
        $response = new Response(200, [], json_encode($testMeta));
        $endpointResponse = EndpointResponse::fromResponse($response);
        $basicMeta = $endpointResponse->getMetaData();

        $this->assertInstanceOf(EndpointResponse::class, $endpointResponse);
        $this->assertInstanceOf(MetaData::class, $basicMeta);

        $this->assertEquals($testMeta['request_id'], $basicMeta->getRequestId());
        $this->assertEquals($testMeta['lease_id'], $basicMeta->getLeaseId());
        $this->assertEquals($testMeta['renewable'], $basicMeta->getRenewable());
        $this->assertEquals($testMeta['lease_duration'], $basicMeta->getLeaseDuration());
        $this->assertEquals($testMeta['wrap_info'], $basicMeta->getWrapInfo());
        $this->assertEquals($testMeta['warnings'], $basicMeta->getWarnings());
        $this->assertEquals((object) $testMeta['auth'], $basicMeta->getAuth());
        $this->assertEquals($testMeta['errors'], $basicMeta->getErrors());
    }

    public function testCanGetPopulatePayloadDataFromResponse()
    {
        $response = new Response(200, [], json_encode([
            'data' => [
                'plaintext' => base64_encode('fooPlaintext'),
            ],
        ]));
        $endpointResponse = DecryptDataResponse::fromResponse($response);

        $this->assertInstanceOf(EndpointResponse::class, $endpointResponse);
        $this->assertEquals('fooPlaintext', $endpointResponse->getPlaintext());
    }

    public function testHasErrors()
    {
        $response = new Response(200, [], json_encode([
            'errors' => [],
            'data' => [],
        ]));
        $endpointResponse = DecryptDataResponse::fromResponse($response);
        $this->assertFalse($endpointResponse->hasErrors());

        $response = new Response(200, [], json_encode([
            'errors' => [
                'foo',
                'bar',
            ],
            'data' => [],
        ]));
        $endpointResponse = DecryptDataResponse::fromResponse($response);
        $this->assertTrue($endpointResponse->hasErrors());
    }

    public function testGetErrors()
    {
        $response = new Response(200, [], json_encode([
            'errors' => [
                'foo',
                'bar'
            ],
            'data' => [],
        ]));
        $endpointResponse = DecryptDataResponse::fromResponse($response);
        $this->assertEquals(['foo', 'bar'], $endpointResponse->getMetaData()->getErrors());
    }
}
