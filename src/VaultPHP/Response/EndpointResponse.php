<?php

namespace VaultPHP\Response;

use Psr\Http\Message\ResponseInterface;
use VaultPHP\SecretEngines\Traits\PopulateDataTrait;

/**
 * Class EndpointResponse
 * @package VaultPHP\Response
 */
class EndpointResponse implements EndpointResponseInterface
{
    use PopulateDataTrait;

    /** @var MetaData */
    protected MetaData $metaData;

    /**
     * EndpointResponse constructor.
     * @param object|array $data
     * @param array $meta
     */
    final public function __construct(object|array $data = [], array $meta = [])
    {
        $this->metaData = new MetaData($meta);
        $this->populateData($data);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected static function getResponseContent(ResponseInterface $response): array
    {
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
    #[\Override]
    public static function fromResponse(ResponseInterface $response): static
    {
        $metaData = static::getResponseContent($response);

        /** @var object|array $domainData */
        $domainData = $metaData['data'] ?? [];
        unset($metaData['data']);

        return new static($domainData, $metaData);
    }

    /**
     * @param ResponseInterface $response
     * @return BulkEndpointResponse
     */
    static function fromBulkResponse(ResponseInterface $response): BulkEndpointResponse
    {
        $metaData = static::getResponseContent($response);

        /** @var object|array $domainData */
        $domainData = $metaData['data'] ?? [];
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
     * @return MetaData
     */
    #[\Override]
    public function getMetaData(): MetaData
    {
        return $this->metaData;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->getMetaData()->hasErrors();
    }
}
