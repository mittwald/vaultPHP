# PHP Hashicorp Vault Client

![Tests](https://github.com/mittwald/vaultPHP/workflows/CI%20Tests/badge.svg?branch=master)

PHP Client Library for the Hashicorp Vault Service. 
This Client follows the Request and Response Data equal to the Hashicorp Vault Client Documentation.
- Authentication https://www.vaultproject.io/api-docs/auth
- Secret Engines https://www.vaultproject.io/api-docs/secret

Feel free to open Pull Requests to add improvements or missing functionality.

## Installation

### Composer
`composer require mittwald/vault-php`

## Implemented Functionality:
- Auth
  - User/Password
  - Token
  - Kubernetes
  - AppRole
- Secret Engines
  - Transit Engine
    - Encrypt/Decrypt
    - Update Key Config
    - Create Key
    - Delete Key
    - List Keys

## Basic Usage

```php
// setting up independent http client 
$httpClient = new Client();

// setting up vault auth provider
$auth = new Token('foo');

// creating the vault request client
$client = new VaultClient(
    $httpClient,
    $auth,
    'http://127.0.0.1:8200'
);

// selecting the desired secret engine
// e.g. Transit Secret Engine
$api = new Transit($client);

// calling specific endpoint
$response = $api->listKeys();

//reading results
var_dump($response->getKeys());
//...
//...
//Profit...
```

#### VaultClient

````php
public function __construct(
    HttpClient $httpClient,
    AuthenticationProviderInterface $authProvider,
    string $apiHost
)
````

`HttpClient` takes every PSR-18 compliant HTTP Client Adapter like `"php-http/curl-client": "^1.7"`

`AuthenticationProviderInterface` Authentication Provider from `/authentication/provider/*`

`$apiHost` Hashicorp Vault REST Endpoint URL

## Bulk Requests
Using Bulk Requests also requires to iterate through the Response
and calling `hasErrors` within the `MetaData` of each Bulk Item to ensure it was processed successfully.

## Exceptions
Calling library methods will throw exceptions, indicating where ever invalid data was provided
or HTTP errors occurred or Vault Generic Endpoint Errors are encountered.
___

`VaultException`

Generic Root Exception where every exception in this library extends from.
___

`VaultHttpException`

Exception will thrown when something inside the HTTP handling will cause an error.
___

`VaultAuthenticationException`

Will be thrown when API Endpoint Authentication fails.
___

`VaultResponseException`

Will be thrown on 5xx status code errors.
___

`InvalidRouteException`

Calling an Invalid/Non Existing/Disabled Vault API Endpoint will throw this Exception.
___

`InvalidDataException`

Exception indicates a failed server payload validation. 

___

`KeyNameNotFoundException`

Will be thrown when trying to request an API Endpoint where the Key Name - that is indicated within the url - will not exist.
