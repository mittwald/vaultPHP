<?php

namespace VaultPHP\SecretEngines\Engines\Transit;

/**
 * Class EncryptionType
 * @package VaultPHP\SecretEngines\Transit
 */
enum EncryptionType: string
{
    case AES_128_GCM_96 = "aes128-gcm96";
    case AES_256_GCM_96 = "aes256-gcm96";
    case CHA_CHA_20_POLY_1305 = "chacha20-poly1305";
    case ED_25519 = "ed25519";
    case ECDSA_P256 = "ecdsa-p256";
    case ECDSA_P384 = "ecdsa-p384";
    case ECDSA_P521 = "ecdsa-p521";
    case RSA_2048 = "rsa-2048";
    case RSA_3072 = "rsa-3072";
    case RSA_4096 = "rsa-4096";
}
