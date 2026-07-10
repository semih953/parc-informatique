<?php
/**
 * Chiffrement AES-256-CBC pour les données sensibles
 * Parc Informatique DITIB France
 *
 * La clé est lue depuis la variable d'environnement DITIB_CRYPTO_KEY
 * Définir dans le shell avant de lancer le serveur :
 *   export DITIB_CRYPTO_KEY="une-cle-longue-et-aleatoire-de-32-chars-minimum"
 *   php -S localhost:8000 router.php
 *
 * Ou créer un fichier .env.php (exclu du repo via .gitignore) :
 *   putenv('DITIB_CRYPTO_KEY=votre-cle-secrete');
 */

// Charger .env.php s'il existe (alternative simple à une vraie lib dotenv)
$_envFile = __DIR__ . '/.env.php';
if (file_exists($_envFile)) {
    require_once $_envFile;
}

function _getCryptoKey(): string {
    $raw = getenv('DITIB_CRYPTO_KEY');
    if (!$raw || strlen($raw) < 16) {
        // En dev sans variable définie, on bloque explicitement plutôt que
        // d'utiliser une clé en dur dans le code.
        error_log('[DITIB] DITIB_CRYPTO_KEY non définie ou trop courte. Chiffrement impossible.');
        http_response_code(500);
        die('Erreur de configuration : clé de chiffrement manquante. Contactez l\'administrateur.');
    }
    return hash('sha256', $raw, true);
}

function encryptData($data): string {
    if (empty($data)) return '';
    $key = _getCryptoKey();
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv) . '.' . base64_encode($encrypted);
}

function decryptData($data): string {
    if (empty($data)) return '';
    $parts = explode('.', $data, 2);
    if (count($parts) !== 2) return $data;
    $key = _getCryptoKey();
    $iv = base64_decode($parts[0]);
    $encrypted = base64_decode($parts[1]);
    $result = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $result !== false ? $result : '';
}