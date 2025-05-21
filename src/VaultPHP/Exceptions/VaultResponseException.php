<?php

namespace VaultPHP\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VaultPHP\Response\EndpointResponse;

/**
 * Class VaultResponseException
 * @package VaultPHP\Exceptions
 */
class VaultResponseException extends VaultException
{
    /** @var RequestInterface */
    private RequestInterface $request;

    /** @var ResponseInterface */
    private ResponseInterface $response;

    /**
     * VaultResponseException constructor.
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param \Throwable $prevException
     */
    public function __construct(ResponseInterface $response, RequestInterface $request, $prevException = null)
    {
        $this->response = $response;
        $this->request = $request;

        $parsedResponse = EndpointResponse::fromResponse($response);
        $returnedErrors = $parsedResponse->getMetaData()->getErrors();
        $errors = implode(', ', $returnedErrors);

        parent::__construct($errors, $response->getStatusCode(), $prevException);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
