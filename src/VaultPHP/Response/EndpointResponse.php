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
    private $basicMetaResponse;

    /**
     * EndpointResponse constructor.
     * @param array|object $data
     */
    public function __construct($data = [])
    {
        $this->basicMetaResponse = new BasicMetaResponse();
        $this->populateData($data);
    }

    /**
     * @param $response
     * @return array
     */
    private static function getResponseContent(ResponseInterface $response) {
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
    static function fromResponse(ResponseInterface $response)
    {
        $metaData = static::getResponseContent($response);

        /** @var object|array $domainData */
        $domainData = isset($metaData['data']) ? $metaData['data'] : [];
        unset($metaData['data']);

        $responseDTO = new static($domainData);
        $responseDTO->basicMetaResponse = new BasicMetaResponse($metaData);

        return $responseDTO;
    }

    /**
     * @param ResponseInterface $response
     * @return static[]
     */
    static function fromBulkResponse(ResponseInterface $response)
    {
        $resultArray = [];
        $metaData = static::getResponseContent($response);

        /** @var object $domainData */
        $domainData = isset($metaData['data']) ? $metaData['data'] : [];
        unset($metaData['data']);

        if ($domainData && is_array($domainData->batch_results)) {
            /** @var object $batchResult */
            foreach($domainData->batch_results as $batchResult) {
                /** @var array $batchMetaData */
                $batchMetaData = $metaData;

                if (isset($batchResult->error)) {
                    /** @var array $currentErrors */
                    $currentErrors = isset($metaData['errors']) ? $metaData['errors'] : [];
                    array_push($currentErrors, $batchResult->error);

                    $batchMetaData['errors'] = $currentErrors;
                }

                $responseDTO = new static($batchResult);
                $responseDTO->basicMetaResponse = new BasicMetaResponse($batchMetaData);
                $resultArray[] = $responseDTO;
            }
        }

        return $resultArray;
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
}
