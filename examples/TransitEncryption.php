<?php

namespace Examples;

use Http\Client\Curl\Client;
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

require_once __DIR__ . '/../vendor/autoload.php';

// setting up curl http client with SSL
$httpClient = new Client(null, null, [
    CURLOPT_SSLCERT => './ssl.pem',
    CURLOPT_SSLCERTTYPE => 'PEM',
    CURLOPT_SSLCERTPASSWD => 'fooBar',
]);

// provide hashicorp vault auth
$authenticationProvider = new Token('test');

// initalize the vault request client
$vaultClient = new VaultClient(
    $httpClient,
    $authenticationProvider,
    'https://127.0.0.1:8200'
);

// choose your secret engine api
$transitApi = new Transit($vaultClient);

// do fancy stuff
try {
    // create key
    $exampleKey = new CreateKeyRequest('exampleKeyName');
    $exampleKey->setType(EncryptionType::CHA_CHA_20_POLY_1305);
    $transitApi->createKey($exampleKey);

    // list keys
    $listKeyResponse = $transitApi->listKeys();
    var_dump($listKeyResponse->getKeys());

    // encrypt data
    $encryptExample = new EncryptDataRequest('exampleKeyName', 'encryptMe');
    $encryptResponse = $transitApi->encryptData($encryptExample);

    var_dump($encryptResponse->getCiphertext());

    // decrypt data
    $decryptExample = new DecryptDataRequest('exampleKeyName', $encryptResponse->getCiphertext());
    $decryptResponse = $transitApi->decryptData($decryptExample);

    var_dump($decryptResponse->getPlaintext());

    // update key config and allow deletion
    $keyConfigExample = new UpdateKeyConfigRequest('exampleKeyName');
    $keyConfigExample->setDeletionAllowed(true);
    $transitApi->updateKeyConfig($keyConfigExample);

    // delete key
    $transitApi->deleteKey('exampleKeyName');

    // list keys
    $listKeyResponse = $transitApi->listKeys();
    var_dump($listKeyResponse->getKeys());

} catch (VaultResponseException $exception) {
    var_dump($exception->getMessage());
    var_dump($exception->getResponse());
    var_dump($exception->getRequest());

} catch (VaultException $exception) {
    var_dump($exception->getMessage());
}
