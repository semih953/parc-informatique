<?php
require_once __DIR__ . '/security.php';

// ===== VÉRIFICATION DE CONNEXION =====
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// ===== EXPIRATION APRÈS 30 MIN D'INACTIVITÉ =====
$timeout = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}
$_SESSION['last_activity'] = time();

// ===== LIAISON DE SESSION AU NAVIGATEUR (limite le vol de cookie) =====
$agent_hash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
if (!isset($_SESSION['agent_hash'])) {
    $_SESSION['agent_hash'] = $agent_hash;
} elseif (!hash_equals($_SESSION['agent_hash'], $agent_hash)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// ===== RÉGÉNÉRATION PÉRIODIQUE DE L'ID DE SESSION (anti-fixation) =====
if (!isset($_SESSION['regenerated_at']) || (time() - $_SESSION['regenerated_at']) > 900) {
    session_regenerate_id(true);
    $_SESSION['regenerated_at'] = time();
}

// ===== VÉRIFICATION CSRF AUTOMATIQUE SUR TOUS LES POST =====
csrf_verify();

// ===== LE COMPTE EXISTE TOUJOURS ? (supprimé par un admin = déconnexion) =====
require_once __DIR__ . '/users-lib.php';
$__current_user = current_user();
if ($__current_user === null) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// ===== CHANGEMENT DE MOT DE PASSE OBLIGATOIRE (mot de passe provisoire) =====
if (!empty($__current_user['must_change']) && basename($_SERVER['SCRIPT_NAME']) !== 'changer-mdp.php') {
    header('Location: changer-mdp.php?force=1');
    exit;
}