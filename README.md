# VaultPHP

[![Latest Release](https://img.shields.io/github/release/mittwald/vaultPHP.svg)](https://github.com/mittwald/vaultPHP/releases)
[![CI](https://github.com/mittwald/vaultPHP/workflows/CI%20Tests/badge.svg)](https://github.com/mittwald/vaultPHP/actions)
[![License: MIT](https://img.shields.io/github/license/mittwald/vaultPHP.svg)](LICENSE)

A modern PHP client for [HashiCorp Vault](https://www.vaultproject.io/) — unlock secure secrets management in your PHP applications.

---

## Features

- **API Client for HashiCorp Vault**  
  Simple and intuitive interface for Vault HTTP API.
- **Bulk Operations**  
  Perform read/write operations on multiple secrets in a single workflow for efficiency.
- **Authentication Support**  
  Compatible with popular Vault auth backends (Token, AppRole, User/Password, etc.).
- **Secret Engines**  
  Easy interaction with common secret engines (Transit, etc.).
- **Typed Responses**  
  Strong-typed, doctrine-based responses for safer PHP development.
- **Extendable & PSR-compliant**  
  Easily extend class behaviors and integrate with PSR-18 HTTP clients.

---

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require mittwald/vault-php
```

---

## Usage

Below is a basic example of how to interact with Vault using this library:

```php
<?php

require 'vendor/autoload.php';

use Mittwald\Vault\Client;
use Mittwald\Vault\Authentication\Token;
use Http\Client\Curl\Client;

// setting up independent http client 
$httpClient = new Client();

// setting up desired vault strategy
$auth = new Token('dummyToken');

// Initialize Vault client
$client = new Client(
    $httpClient,
    $auth,
    'https://vault.example.com:1337'
);

// List all keys from Transit Secret engine
$api = new Transit($client);
var_dump($api->listKeys());
```

For more advanced use (custom HTTP clients, other auth methods, etc.), see the [`examples/`](examples/) directory.

---

## Supported Vault Operations

- Authentication
  - Token
  - AppRole
  - User/Password
  - Kubernetes
- Transit Secret Engine
  - Encrypt/Decrypt
  - Update Key Config
  - Create Key
  - Delete Key
  - List Keys
  - Sign Data

---

## Configuration

You can inject any [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/) for maximum flexibility:

```php
$client = new Client(
    $yourPsr18Client,
    $auth,
    'https://vault.example.com:1337'
);
```

---

## Testing

To run the test suite:

```bash
composer install
composer test
```

---

## Security

If you discover any security issues, please see [`SECURITY.md`](SECURITY.md) for responsible disclosure guidelines.

---

## License

This library is Open Source and distributed under the [MIT license](LICENSE).

---

## Links

- [HashiCorp Vault](https://www.vaultproject.io/)
- [mittwald/vaultPHP on GitHub](https://github.com/mittwald/vaultPHP)

---
