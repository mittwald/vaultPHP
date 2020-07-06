<?php

namespace Test\VaultPHP\Response;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VaultPHP\Response\BasicMetaResponse;
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
        $response = new Response(200, [], json_encode([
            'errors' => [
                'metaDataError',
                'metaDataError2',
            ],
        ]));
        $endpointResponse = EndpointResponse::fromResponse($response);
        $basicMeta = $endpointResponse->getBasicMetaResponse();

        $this->assertInstanceOf(EndpointResponse::class, $endpointResponse);
        $this->assertInstanceOf(BasicMetaResponse::class, $basicMeta);
        $this->assertEquals(
            ['metaDataError', 'metaDataError2'],
            $basicMeta->getErrors()
        );
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

    public function testCanGetPopulateMetaDataFromBulkResponse()
    {
        $response = new Response(200, [], json_encode([
            'errors' => [
                'metaDataError',
                'metaDataError2',
            ],
            'data' => [
                'batch_results' => [
                    [],
                    [],
                ],
            ],
        ]));
        $arrayEndpointResponse = EndpointResponse::fromBulkResponse($response);
        $this->assertSame(2, count($arrayEndpointResponse));

        foreach($arrayEndpointResponse as $response) {
            $basicMeta = $response->getBasicMetaResponse();

            $this->assertInstanceOf(EndpointResponse::class, $response);
            $this->assertInstanceOf(BasicMetaResponse::class, $basicMeta);
            $this->assertEquals(
                ['metaDataError', 'metaDataError2'],
                $basicMeta->getErrors()
            );
        }
    }

    public function testBulkErrorsWillBeMergedInMetaDataErrors()
    {
        $batchErrors = [
            ['error' => 'OH NO'],
            ['error' => 'WHHAAT'],
            [],
        ];

        $response = new Response(200, [], json_encode([
            'errors' => [
                'metaDataError',
                'metaDataError2',
            ],
            'data' => [
                'batch_results' => $batchErrors,
            ],
        ]));

        $arrayEndpointResponse = EndpointResponse::fromBulkResponse($response);
        foreach($arrayEndpointResponse as $response) {
            $basicMeta = $response->getBasicMetaResponse();

            $this->assertEquals(
                array_merge(
                    ['metaDataError', 'metaDataError2'],
                    array_values(current($batchErrors))
                ),
                $basicMeta->getErrors()
            );
            next($batchErrors);
        }
    }

    public function testBulkPayloadWillBePopulatedToResponseClass()
    {
        $batchResponse = [
            ['plaintext' => base64_encode('OH NO')],
            ['plaintext' => base64_encode('WHHAAT')],
        ];

        $response = new Response(200, [], json_encode([
            'data' => [
                'batch_results' => $batchResponse,
            ],
        ]));

        $arrayEndpointResponse = DecryptDataResponse::fromBulkResponse($response);
        foreach($arrayEndpointResponse as $bulkResponse) {
            $expected = array_map('base64_decode', current($batchResponse));
            $this->assertEquals(current($expected), $bulkResponse->getPlaintext());
            next($batchResponse);
        }
    }
}
