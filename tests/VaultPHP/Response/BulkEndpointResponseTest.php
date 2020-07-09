<?php

namespace Test\VaultPHP\Response;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Response\MetaData;
use VaultPHP\Response\BulkEndpointResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\DecryptDataResponse;

/**
 * Class BulkEndpointResponseTest
 * @package Test\VaultPHP\Response
 */
final class BulkEndpointResponseTest extends TestCase
{
    public function testCanInteractWithBulkResponseLikeArray()
    {
        $response = new Response(200, [], json_encode([
            'data' => [
                'batch_results' => [
                    [],
                    [],
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $this->assertInstanceOf(BulkEndpointResponse::class, $bulkResponses);
        $this->assertTrue(is_array($bulkResponses->getBatchResults()));

        // test foreach
        foreach ($bulkResponses as $batchResponse) {
            $this->assertInstanceOf(DecryptDataResponse::class, $batchResponse);
        }

        // can count
        $this->assertCount(3, $bulkResponses);

        $bulkResponses->rewind();
        $this->assertTrue($bulkResponses->valid());

        // can interact with index
        $this->assertInstanceOf(DecryptDataResponse::class, $bulkResponses[2]);

        // can iterate
        $this->assertSame($bulkResponses[0], $bulkResponses->current());

        $bulkResponses->next();
        $this->assertSame($bulkResponses[1], $bulkResponses->current());

        $bulkResponses->next();
        $this->assertSame($bulkResponses[2], $bulkResponses->current());

        $bulkResponses->next();
        $this->assertFalse($bulkResponses->valid());

        $this->assertEquals(3, $bulkResponses->key());
        $this->assertTrue(isset($bulkResponses[0]));
        $this->assertFalse(isset($bulkResponses[3]));
    }

    public function testCantWriteToArrayStyleObject() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('readonly');

        $response = new Response(200, [], json_encode([
            'data' => [
                'batch_results' => [
                    [],
                    [],
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $bulkResponses[1] = "foo";
    }

    public function testCantDeleteFromArrayStyleObject() {
        $this->expectException(VaultException::class);
        $this->expectExceptionMessage('readonly');

        $response = new Response(200, [], json_encode([
            'data' => [
                'batch_results' => [
                    [],
                    [],
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        unset($bulkResponses[1]);
    }

    public function testHasErrors() {
        $response = new Response(200, [], json_encode([
            'errors' => [],
            'data' => [
                'batch_results' => [
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $this->assertFalse($bulkResponses->hasErrors());

        $response = new Response(200, [], json_encode([
            'errors' => [
                'oh no'
            ],
            'data' => [
                'batch_results' => [
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $this->assertTrue($bulkResponses->hasErrors());

        $response = new Response(200, [], json_encode([
            'errors' => [
            ],
            'data' => [
                'batch_results' => [
                    [],
                    [
                        'error' => 'oh no'
                    ],
                    [],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $this->assertTrue($bulkResponses->hasErrors());
    }

    public function testGetErrors() {
        $response = new Response(200, [], json_encode([
            'errors' => [
                'foo',
                'bar',
            ],
            'data' => [
                'batch_results' => [
                    [],
                    ['error' => 'baz, buz'],
                    [],
                    ['error' => 'bam'],
                ],
            ],
        ]));
        $bulkResponses = DecryptDataResponse::fromBulkResponse($response);
        $this->assertEquals([], $bulkResponses[0]->getMetaData()->getErrors());
        $this->assertEquals(['baz', 'buz'], $bulkResponses[1]->getMetaData()->getErrors());
        $this->assertEquals([], $bulkResponses[2]->getMetaData()->getErrors());
        $this->assertEquals(['bam'], $bulkResponses[3]->getMetaData()->getErrors());

        $this->assertEquals(['foo', 'bar'], $bulkResponses->getMetaData()->getErrors());
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
                    [
                        'error' => 'batchError'
                    ],
                    [],
                    [
                        'error' => 'batchError2'
                    ],
                ],
            ],
        ]));
        $arrayEndpointResponse = DecryptDataResponse::fromBulkResponse($response);
        $this->assertSame(3, count($arrayEndpointResponse));

        $basicMeta = $arrayEndpointResponse->getMetaData();
        $this->assertEquals(['metaDataError', 'metaDataError2'], $basicMeta->getErrors());
        $this->assertTrue($arrayEndpointResponse->hasErrors());

        /** @var DecryptDataResponse $batchResponse */
        foreach($arrayEndpointResponse as $batchResponse) {
            $this->assertInstanceOf(DecryptDataResponse::class, $batchResponse);
            $this->assertInstanceOf(MetaData::class, $batchResponse->getMetaData());
        }

        $this->assertEquals(['batchError'], $arrayEndpointResponse[0]->getMetaData()->getErrors());
        $this->assertEquals([], $arrayEndpointResponse[1]->getMetaData()->getErrors());
        $this->assertEquals(['batchError2'], $arrayEndpointResponse[2]->getMetaData()->getErrors());
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
