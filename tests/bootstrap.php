<?php

require_once __DIR__ . '/../vendor/autoload.php';

const TEST_VAULT_ENDPOINT = 'http://vault:8200';

error_reporting(E_ALL);

// since this is a repo that should
// run under php 7 and 5.6 we hide
// dep warnings should the test show
// how this lib performes with php 7
if(PHP_VERSION_ID >= 70400) {
    error_reporting(E_ALL ^ E_DEPRECATED);
}
