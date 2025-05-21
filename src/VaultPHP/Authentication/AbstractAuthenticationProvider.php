<?php
declare(strict_types=1);

namespace VaultPHP\Authentication;

use VaultPHP\Exceptions\InvalidDataException;
use VaultPHP\Exceptions\InvalidRouteException;
use VaultPHP\Exceptions\VaultAuthenticationException;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultHttpException;
use VaultPHP\SecretEngines\Interfaces\ResourceRequestInterface;
use VaultPHP\VaultClient;

/**
 * Class AbstractAuthenticationProvider
 * @package VaultPHP\Authentication
 */
abstract class AbstractAuthenticationProvider implements AuthenticationProviderInterface
{
    /** @var VaultClient|null */
    private ?VaultClient $vaultClient = null;

    /**
     * @param VaultClient $VaultClient
     * @return void
     */
    #[\Override]
    public function setVaultClient(VaultClient $VaultClient): void
    {
        $this->vaultClient = $VaultClient;
    }

    /**
     * @throws VaultException
     * @return VaultClient
     */
    private function getVaultClient(): VaultClient
    {
        if (!$this->vaultClient) {
            throw new VaultException('Trying to request the VaultClient before initialization');
        }

        return $this->vaultClient;
    }

    /**
     * @throws InvalidRouteException
     * @throws VaultHttpException
     * @throws VaultException
     * @throws InvalidDataException
     * @throws VaultAuthenticationException
     */
    protected function sendApiRequest(string $method, string $endpoint, string $returnClass, ResourceRequestInterface|array $data = []): mixed
    {
        return $this->getVaultClient()->sendApiRequest(
            $method,
            $endpoint,
            $returnClass,
            $data,
            false,
        );
    }
}
