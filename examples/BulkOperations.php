<?php

namespace Examples;

use VaultPHP\Authentication\Provider\Token;
use VaultPHP\Exceptions\VaultException;
use VaultPHP\Exceptions\VaultResponseException;
use VaultPHP\SecretEngines\Engines\Transit\Request\CreateKeyRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptData;
use VaultPHP\SecretEngines\Engines\Transit\Request\EncryptData\EncryptDataBulkRequest;
use VaultPHP\SecretEngines\Engines\Transit\Request\UpdateKeyConfigRequest;
use VaultPHP\SecretEngines\Engines\Transit\Response\EncryptDataResponse;
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


// do fancy stuff
try {
    // create key
    $exampleKey = new CreateKeyRequest('exampleKeyName');
    $exampleKey->setType(EncryptionType::RSA_2048);
    $transitApi->createKey($exampleKey);

    $encryptRequest = new EncryptDataBulkRequest('exampleKeyName');
    $encryptRequest->addBulkRequests([
        new EncryptData('cryptMeBabyOneMoreTime::1'),
        new EncryptData('cryptMeBabyOneMoreTime::2'),
        new EncryptData('cryptMeBabyOneMoreTime::3'),
        new EncryptData('cryptMeBabyOneMoreTime::4'),
    ]);
    $encryptBulkResponse = $transitApi->encryptDataBulk($encryptRequest);

    // indicates that one or more bulk results contains errors
    var_dump($encryptBulkResponse->hasErrors());

    /** @var EncryptDataResponse $bulkResult */
    foreach($encryptBulkResponse as $bulkResult) {
        // TO ENSURE EVERY BULK ENCRYPT REQUEST WAS SUCCESSFULLY PROCESSED
        // YOU HAVE TO CHECK FOR ERRORS OF EVERY BULK ITEM
        if (!$bulkResult->getMetaData()->hasErrors()) {
            var_dump($bulkResult->getCiphertext());
        }
    }

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
