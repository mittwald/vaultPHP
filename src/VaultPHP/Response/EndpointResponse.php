<?php

declare(strict_types=1);

namespace VaultPHP\Response;

use Psr\Http\Message\ResponseInterface;
use VaultPHP\SecretEngines\Traits\PopulateDataTrait;

/**
 * Class EndpointResponse.
 */
class EndpointResponse implements EndpointResponseInterface
{
    use PopulateDataTrait;

    protected MetaData $metaData;

    /**
     * EndpointResponse constructor.
     */
    final public function __construct(array|object $data = [], array $meta = [])
    {
        $this->metaData = new MetaData($meta);
        $this->populateData($data);
    }

    #[\Override]
    public static function fromResponse(ResponseInterface $response): static
    {
        $metaData = static::getResponseContent($response);

        /** @var array|object $domainData */
        $domainData = $metaData['data'] ?? [];
        unset($metaData['data']);

        return new static($domainData, $metaData);
    }

    public static function fromBulkResponse(ResponseInterface $response): BulkEndpointResponse
    {
        $metaData = static::getResponseContent($response);

        /** @var array|object $domainData */
        $domainData = $metaData['data'] ?? [];
        unset($metaData['data']);

        $responseDTO = new BulkEndpointResponse($domainData, $metaData);
        $responseDTO->batch_results = array_map(function ($batchResult) {
            /** @var object $batchResult */
            $errors = isset($batchResult->error) && $batchResult->error ? explode(', ', (string) $batchResult->error) : [];

            return new static($batchResult, [
                'errors' => $errors,
            ]);
        }, $responseDTO->batch_results);

        return $responseDTO;
    }

    #[\Override]
    public function getMetaData(): MetaData
    {
        return $this->metaData;
    }

    public function hasErrors(): bool
    {
        return $this->getMetaData()->hasErrors();
    }

    protected static function getResponseContent(ResponseInterface $response): array
    {
        $responseBody = $response->getBody();
        $responseBody->rewind();
        $responseBodyContents = $responseBody->getContents();

        // cast to array because we only want the first root
        // as array and not the complete response
        return (array) json_decode($responseBodyContents);
    }
}
