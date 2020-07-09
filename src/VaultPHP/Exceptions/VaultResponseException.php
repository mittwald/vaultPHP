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
    private $request;

    /** @var ResponseInterface */
    private $response;

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
        $errors = implode(', ',  is_array($returnedErrors) ? $returnedErrors : []);

        parent::__construct($errors, $response->getStatusCode(), $prevException);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
