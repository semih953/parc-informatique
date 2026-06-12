<?php
/**
 * security.php - Configuration de sécurité centrale
 * Parc Informatique DITIB France
 *
 * À inclure en PREMIER dans login.php et auth.php
 */

// ===== SESSIONS SÉCURISÉES =====
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');      // Refuse les IDs de session non générés par le serveur
    ini_set('session.cookie_httponly', '1');      // Cookie inaccessible en JavaScript (anti-XSS)
    ini_set('session.cookie_samesite', 'Strict'); // Cookie non envoyé depuis d'autres sites (anti-CSRF)
    ini_set('session.use_only_cookies', '1');     // Pas d'ID de session dans l'URL

    // Cookie "secure" uniquement si HTTPS (à activer en production)
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

// ===== EN-TÊTES HTTP DE SÉCURITÉ =====
header('X-Frame-Options: DENY');                 // Anti-clickjacking
header('X-Content-Type-Options: nosniff');       // Empêche le navigateur de "deviner" les types de fichiers
header('Referrer-Policy: same-origin');          // Ne divulgue pas les URLs internes
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src https://fonts.gstatic.com; img-src 'self' data:; frame-ancestors 'none'");

// ===== PROTECTION CSRF =====

/**
 * Génère (ou récupère) le jeton CSRF de la session
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Champ caché à insérer dans chaque formulaire POST :
 * <?php echo csrf_field(); ?>
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Vérifie le jeton CSRF sur toute requête POST.
 * Bloque la requête si le jeton est absent ou invalide.
 */
function csrf_verify() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Requête refusée : jeton de sécurité invalide. Rechargez la page et réessayez.');
        }
    }
}