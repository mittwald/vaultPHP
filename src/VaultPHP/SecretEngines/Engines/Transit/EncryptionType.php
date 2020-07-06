<?php

namespace VaultPHP\SecretEngines\Engines\Transit;

/**
 * Class EncryptionType
 * @package VaultPHP\SecretEngines\Transit
 */
abstract class EncryptionType
{
    const AES_128_GCM_96 = "aes128-gcm96";
    const AES_256_GCM_96 = "aes256-gcm96";
    const CHA_CHA_20_POLY_1305 = "chacha20-poly1305";
    const ED_25519 = "ed25519";
    const ECDSA_P256 = "ecdsa-p256";
    const ECDSA_P384 = "ecdsa-p384";
    const ECDSA_P521 = "ecdsa-p521";
    const RSA_2048 = "rsa-2048";
    const RSA_3072 = "rsa-3072";
    const RSA_4096 = "rsa-4096";
}
