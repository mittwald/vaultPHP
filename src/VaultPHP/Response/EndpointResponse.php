<?php

namespace VaultPHP\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class EndpointResponse
 * @package VaultPHP\Response
 */
class EndpointResponse implements EndpointResponseInterface
{
    /** @var BasicMetaResponse */
    protected $basicMetaResponse;

    /**
     * EndpointResponse constructor.
     * @param array|object $data
     * @param array $meta
     */
    public function __construct($data = [], $meta = [])
    {
        $this->basicMetaResponse = new BasicMetaResponse($meta);
        $this->populateData($data);
    }

    /**
     * @param $response
     * @return array
     */
    protected static function getResponseContent(ResponseInterface $response) {
        $responseBody = $response->getBody();
        $responseBody->rewind();
        $responseBodyContents = $responseBody->getContents();

        // cast to array because we only want the first root
        // as array and not the complete response
        return (array) json_decode($responseBodyContents);
    }

    /**
     * @param ResponseInterface $response
     * @return static
     */
    public static function fromResponse(ResponseInterface $response)
    {
        $metaData = static::getResponseContent($response);

        /** @var object|array $domainData */
        $domainData = isset($metaData['data']) ? $metaData['data'] : [];
        unset($metaData['data']);

        return new static($domainData, $metaData);
    }

    /**
     * @param ResponseInterface $response
     * @return BulkEndpointResponse
     */
    static function fromBulkResponse(ResponseInterface $response)
    {
        $metaData = static::getResponseContent($response);

        /** @var object|array $domainData */
        $domainData = isset($metaData['data']) ? $metaData['data'] : [];
        unset($metaData['data']);

        $responseDTO = new BulkEndpointResponse($domainData, $metaData);
        $responseDTO->batch_results = array_map(function($batchResult) {
            /** @var object $batchResult */

            $errors = isset($batchResult->error) && $batchResult->error ? explode(', ', (string) $batchResult->error) : [];
            return new static($batchResult, [
                'errors' => $errors,
            ]);
        }, $responseDTO->batch_results);

        return $responseDTO;
    }

    /**
     * @param array|object $data
     * @return void
     */
    private function populateData($data)
    {
        /** @var string $key */
        /** @var mixed $value */
        foreach ($data as $key => $value) {
            if (property_exists(static::class, (string) $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return BasicMetaResponse
     */
    public function getBasicMetaResponse()
    {
        return $this->basicMetaResponse;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->getBasicMetaResponse()->hasErrors();
    }
}
