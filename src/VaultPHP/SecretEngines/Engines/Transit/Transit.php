<?php

namespace VaultPHP\SecretEngines\Engines\Transit;

use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\Response\BulkEndpointResponse;
use VaultPHP\Response\EndpointResponse;
use VaultPHP\SecretEngines\AbstractSecretEngine;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\SignDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\CreateKeyResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\DecryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\DeleteKeyResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\EncryptDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\ListKeysResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\SignDataResponse;
use VaultPHP\SecretEngines\Engines\Transit\Response\UpdateKeyConfigResponse;

/**
 * Class Transit
 * @package VaultPHP\SecretEngines\Transit
 */
final class Transit extends AbstractSecretEngine
{
    /**
     * @var string
     */
    private $APIPath = 'transit';

    /**
     * @param string $path
     * @return void
     */
    public function setAPIPath(string $path)
    {
        $this->APIPath = $path;
    }

    /**
     * @param CreateKeyRequest $createKeyRequest
     * @return CreateKeyResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function createKey(CreateKeyRequest $createKeyRequest)
    {
        /** @var CreateKeyResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/keys/%s', $this->APIPath, urlencode($createKeyRequest->getName())),
            CreateKeyResponse::class,
            $createKeyRequest
        );
    }

    /**
     * @param EncryptDataRequest $encryptDataRequest
     * @return EncryptDataResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function encryptData(EncryptDataRequest $encryptDataRequest)
    {
        /** @var EncryptDataResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/encrypt/%s', $this->APIPath, urlencode($encryptDataRequest->getName())),
            EncryptDataResponse::class,
            $encryptDataRequest
        );
    }

    /**
     * @param EncryptDataBulkRequest $encryptDataBulkRequest
     * @return BulkEndpointResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function encryptDataBulk(EncryptDataBulkRequest $encryptDataBulkRequest)
    {
        /** @var BulkEndpointResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/encrypt/%s', $this->APIPath, urlencode($encryptDataBulkRequest->getName())),
            EncryptDataResponse::class,
            $encryptDataBulkRequest
        );
    }

    /**
     * @param DecryptDataRequest $decryptDataRequest
     * @return DecryptDataResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function decryptData(DecryptDataRequest $decryptDataRequest)
    {
        /** @var DecryptDataResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/decrypt/%s', $this->APIPath, urlencode($decryptDataRequest->getName())),
            DecryptDataResponse::class,
            $decryptDataRequest
        );
    }

    /**
     * @param DecryptDataBulkRequest $decryptDataBulkRequest
     * @return BulkEndpointResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     * @throws VaultAuthenticationException
     * @throws VaultHttpException
     */
    public function decryptDataBulk(DecryptDataBulkRequest $decryptDataBulkRequest)
    {
        /** @var BulkEndpointResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/decrypt/%s', $this->APIPath, urlencode($decryptDataBulkRequest->getName())),
            DecryptDataResponse::class,
            $decryptDataBulkRequest
        );
    }

    /**
     * @return ListKeysResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function listKeys()
    {
        /** @var ListKeysResponse */
        return $this->vaultClient->sendApiRequest(
            'LIST',
            sprintf('/v1/%s/keys', $this->APIPath,),
            ListKeysResponse::class,
            []
        );
    }

    /**
     * @param string $name
     * @return EndpointResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function deleteKey($name)
    {
        /** @var EndpointResponse */
        return $this->vaultClient->sendApiRequest(
            'DELETE',
            sprintf('/v1/%s/keys/%s', $this->APIPath, urlencode($name)),
            DeleteKeyResponse::class,
            []
        );
    }

    /**
     * @param UpdateKeyConfigRequest $updateKeyConfigRequest
     * @return UpdateKeyConfigResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function updateKeyConfig(UpdateKeyConfigRequest $updateKeyConfigRequest)
    {
        /** @var UpdateKeyConfigResponse */
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/keys/%s/config', $this->APIPath, urlencode($updateKeyConfigRequest->getName())),
            UpdateKeyConfigResponse::class,
            $updateKeyConfigRequest
        );
    }

    /**
     * @param SignDataRequest $signDataRequest
     * @return SignDataResponse
     * @throws InvalidDataException
     * @throws InvalidRouteException
     * @throws VaultException
     */
    public function sign(SignDataRequest $signDataRequest)
    {
        return $this->vaultClient->sendApiRequest(
            'POST',
            sprintf('/v1/%s/sign/%s/%s', $this->APIPath, urlencode($signDataRequest->getKey()), $signDataRequest->getHashAlgorithm()),
            SignDataResponse::class,
            $signDataRequest
        );
    }
}
