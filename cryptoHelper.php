<?php
/**
 * Chiffrement AES-256-CBC pour les données sensibles
 * Parc Informatique DITIB France
 */

function encryptData($data) {
    if (empty($data)) return '';
    $key = hash('sha256', 'DITIB_PARC_SECURE_2026_KEY', true);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv) . '.' . base64_encode($encrypted);
}

function decryptData($data) {
    if (empty($data)) return '';
    $parts = explode('.', $data, 2);
    if (count($parts) !== 2) return $data;
    $key = hash('sha256', 'DITIB_PARC_SECURE_2026_KEY', true);
    $iv = base64_decode($parts[0]);
    $encrypted = base64_decode($parts[1]);
    $result = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $result !== false ? $result : '';
}
?>