<?php

declare(strict_types=1);

namespace VaultPHP\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VaultPHP\Response\EndpointResponse;

/**
 * Class VaultResponseException.
 */
class VaultResponseException extends VaultException
{
    private RequestInterface $request;

    private ResponseInterface $response;

    /**
     * VaultResponseException constructor.
     *
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
