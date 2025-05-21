<?php

namespace Examples;

use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultResponseException;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\DecryptData\DecryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Transit;
use VaultPHP\SecretEngines\Engines\Transit\EncryptionType;
use VaultPHP\VaultClient;

use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

// setup http client
$httpClient = new Client(['verify' => false]);

// setup authentication provider
$authenticationProvider = new Token('test');

// initialize the vault request client
$vaultClient = new VaultClient(
    $httpClient,
    $authenticationProvider,
    'http://127.0.0.1:8200/transit/',
);

// create eg. Transit API instance
$transitApi = new Transit($vaultClient);

try {
    // create key
    $exampleKey = new CreateKeyRequest('exampleKeyName');
    $exampleKey->setType(EncryptionType::CHA_CHA_20_POLY_1305);
    $transitApi->createKey($exampleKey);

    // list keys
    $listKeyResponse = $transitApi->listKeys();
    var_dump($listKeyResponse->getKeys()); // ["exampleKeyName"]

    // encrypt data
    $encryptExample = new EncryptDataRequest('exampleKeyName', 'encryptMe');
    $encryptResponse = $transitApi->encryptData($encryptExample);
    var_dump($encryptResponse->getCiphertext()); // vault:v1:jt9yxqU2aHd+EIOZs1swB+C3jVLtvyXgpfdfbxi+thNafm0IDQ==

    // decrypt data
    $decryptExample = new DecryptDataRequest('exampleKeyName', $encryptResponse->getCiphertext());
    $decryptResponse = $transitApi->decryptData($decryptExample);
    var_dump($decryptResponse->getPlaintext());  // encryptMe

    // update key config and allow deletion
    $keyConfigExample = new UpdateKeyConfigRequest('exampleKeyName');
    $keyConfigExample->setDeletionAllowed(true);
    $transitApi->updateKeyConfig($keyConfigExample);

    // delete key
    $transitApi->deleteKey('exampleKeyName');

    // list keys
    $listKeyResponse = $transitApi->listKeys();
    var_dump($listKeyResponse->getKeys()); // []

} catch (VaultResponseException $exception) {
    var_dump($exception->getMessage());
    var_dump($exception->getResponse());
    var_dump($exception->getRequest());

} catch (VaultException $exception) {
    var_dump($exception->getMessage());
}
