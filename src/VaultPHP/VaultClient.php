<?php
declare(strict_types=1);

namespace VaultPHP;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VaultPHP\Authentication\AuthenticationMetaData;
use VaultPHP\Authentication\AuthenticationProviderInterface;
use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\KeyNameNotFoundException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Exceptions\VaultResponseException;
use VaultPHP\Response\ApiErrors;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\Response\EndpointResponseInterface;
use VaultPHP\SecretEngines\Interfaces\ArrayExportInterface;
use VaultPHP\SecretEngines\Interfaces\BulkResourceRequestInterface;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;

final class VaultClient
{
    /** @var string */
    public string $apiHost;

    /** @var ClientInterface */
    public ClientInterface $httpClient;

    /** @var AuthenticationProviderInterface */
    public AuthenticationProviderInterface $authProvider;

    /** @var AuthenticationMetaData|null */
    public ?AuthenticationMetaData $authenticationMetaData = null;

    /**
     * @param ClientInterface $httpClient
     * @param AuthenticationProviderInterface $authProvider
     * @param string $apiHost
     */
    public function __construct(ClientInterface $httpClient, AuthenticationProviderInterface $authProvider, string $apiHost)
    {
        $this->httpClient = $httpClient;
        $this->apiHost = $apiHost;

        $this->authProvider = $authProvider;
        $this->authProvider->setVaultClient($this);
    }

    /**
     * @return void
     * @throws VaultAuthenticationException
     */
    private function authenticate(): void
    {
        if (!$this->authenticationMetaData) {
            try {
                $metaData = $this->authProvider->authenticate();

                if (!$metaData instanceof AuthenticationMetaData || !$metaData->isClientTokenPresent()) {
                    throw new VaultException('Client Token is missing');
                }

                $this->authenticationMetaData = $metaData;
            } catch (\Throwable $e) {
                throw new VaultAuthenticationException(
                    sprintf('AuthProvider %s failed to fetch token', get_class($this->authProvider)),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * @param ArrayExportInterface|array $data
     * @return string
     */
    private function extractPayload(ArrayExportInterface|array $data): string
    {
        if ($data instanceof ArrayExportInterface) {
            $data = $data->toArray();
        }

        $encodedData = json_encode($data);
        if ($encodedData === false) {
            return "";
        }

        return $encodedData;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param string $returnClass
     * @param array|ResourceRequestInterface $data
     * @param bool $authRequired
     * @return mixed
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultHttpException
     */
    public function sendApiRequest(
        string $method,
        string $endpoint,
        string $returnClass,
        ResourceRequestInterface|array $data = [],
        bool $authRequired = true
    ): mixed {
        if ($authRequired) {
            $this->authenticate();
        }

        $extractedPayload = $this->extractPayload($data);
        $request = new Request(
            $method,
            $endpoint,
            [],
            $extractedPayload
        );

        $response = $this->sendRequest($request);
        return $this->parseResponse(
            $request,
            $response,
            $returnClass,
            $data instanceof BulkResourceRequestInterface
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string $returnClass
     * @param boolean $isBulkRequest
     * @return mixed
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultAuthenticationException
     * @throws VaultException
     * @throws VaultResponseException
     */
    private function parseResponse(
        RequestInterface  $request,
        ResponseInterface $response,
        string            $returnClass,
        bool $isBulkRequest
    ): mixed {
        $status = $response->getStatusCode();

        /**
         * Looks like psalm can't handle the method exists with static functions
         */
        if (!$isBulkRequest) {
            /** @psalm-suppress ArgumentTypeCoercion */
            if (!method_exists($returnClass, 'fromResponse')) {
                throw new VaultException('Return Class declaration lacks static::fromResponse');
            }

            /** @var EndpointResponse $responseDataDTO */
            $responseDataDTO = $returnClass::fromResponse($response);
        } else {
            /** @psalm-suppress ArgumentTypeCoercion */
            if (!method_exists($returnClass, 'fromBulkResponse')) {
                throw new VaultException('Return Class declaration lacks static::fromBulkResponse');
            }

            /** @var EndpointResponse[] $responseDataDTO */
            $responseDataDTO = $returnClass::fromBulkResponse($response);
        }

        if (!$responseDataDTO instanceof EndpointResponseInterface) {
            throw new VaultException('Result from "fromResponse/fromBulkResponse" isn\'t an instance of EndpointResponse or Array');
        }

        $responseMetaData = $responseDataDTO->getMetaData();
        $responseHasErrors = $responseMetaData->hasErrors();

        if ($status >= 200 && $status < 300) {
            return $responseDataDTO;

        } elseif ($status >= 400 && $status < 500) {
            if ($status === 400) {
                if ($responseMetaData->containsError(ApiErrors::ENCRYPTION_KEY_NOT_FOUND)) {
                    throw new KeyNameNotFoundException($response, $request);
                }
                throw new InvalidDataException($response, $request);
            } elseif ($status === 403) {
                throw new VaultAuthenticationException('Authentication with provided Token failed');
            } elseif ($status === 404) {
                // if 404 and no error this indicates no data for e.g. List
                // makes no sense but hey - the vault rest is a magical unicorn
                if (!$isBulkRequest && !$responseHasErrors) {
                    return $responseDataDTO;
                }

                // otherwise 404 and error object
                // indicates a route that is not defined
                throw new InvalidRouteException($response, $request);
            }
        } elseif ($status >= 500) {
            throw new VaultResponseException($response, $request);
        }

        throw new VaultException(sprintf("server responded with unhandled status code %s", $response->getStatusCode()));
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws VaultException
     * @throws VaultHttpException
     */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        $requestWithDefaults = $this->getDefaultRequest($request);
        try {
            return $this->httpClient->sendRequest($requestWithDefaults);
        } catch (\Throwable $exception) {
            throw new VaultHttpException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @param $request RequestInterface
     * @return RequestInterface
     * @throws VaultException
     */
    private function getDefaultRequest(RequestInterface $request): RequestInterface
    {
        $token = $this->authenticationMetaData ? $this->authenticationMetaData->getClientToken() : '';
        $hostEndpoint = parse_url($this->apiHost);

        if (!is_array($hostEndpoint) || !isset($hostEndpoint['scheme']) || !isset($hostEndpoint['host'])) {
            throw new VaultException('can\'t parse provided apiHost - malformed uri');
        }

        $uriWithHost = $request
            ->getUri()
            ->withScheme($hostEndpoint['scheme'])
            ->withHost($hostEndpoint['host']);

        if (isset($hostEndpoint['port'])) {
            $uriWithHost = $uriWithHost->withPort($hostEndpoint['port']);
        }

        return $request
            ->withUri($uriWithHost)
            ->withAddedHeader('X-Vault-Request', '1')
            ->withAddedHeader('X-Vault-Token', $token)
            ->withAddedHeader('Content-Type', 'application/json');
    }
}
