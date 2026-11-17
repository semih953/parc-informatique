<?php
/**
 * users-lib.php - Gestion des comptes de connexion (users.json)
 * Parc Informatique DITIB France
 *
 * Champs d'un compte :
 *   username      : email (en minuscules)
 *   password_hash : hash bcrypt
 *   role          : "admin" ou "user"
 *   must_change   : true = doit changer son mot de passe à la prochaine connexion
 *   created_at    : date de création
 */

const USERS_FILE = __DIR__ . '/users.json';

function users_load() {
    if (!file_exists(USERS_FILE)) return [];
    $users = json_decode(file_get_contents(USERS_FILE), true) ?? [];

    // Migration : si aucun admin n'existe encore, le premier compte devient admin
    $hasAdmin = false;
    foreach ($users as $u) {
        if (($u['role'] ?? '') === 'admin') { $hasAdmin = true; break; }
    }
    if (!$hasAdmin && count($users) > 0) {
        $users[0]['role'] = 'admin';
        users_save($users);
    }
    return $users;
}

function users_save(array $users) {
    file_put_contents(USERS_FILE, json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function users_normalize($username) {
    return mb_strtolower(trim($username));
}

/** Retourne l'index du compte dans le tableau, ou -1 */
function users_find(array $users, $username) {
    $username = users_normalize($username);
    foreach ($users as $i => $u) {
        if (hash_equals(users_normalize($u['username'] ?? ''), $username)) return $i;
    }
    return -1;
}

function users_count_admins(array $users) {
    $n = 0;
    foreach ($users as $u) if (($u['role'] ?? '') === 'admin') $n++;
    return $n;
}

/** Utilisateur connecté (ou null) */
function current_user() {
    if (empty($_SESSION['username'])) return null;
    $users = users_load();
    $i = users_find($users, $_SESSION['username']);
    return $i >= 0 ? $users[$i] : null;
}

function is_admin() {
    $u = current_user();
    return $u !== null && ($u['role'] ?? '') === 'admin';
}

/** Bloque la page si l'utilisateur n'est pas admin */
function require_admin() {
    if (!is_admin()) {
        http_response_code(403);
        die('Accès refusé : réservé aux administrateurs.');
    }
}

/** Mot de passe provisoire lisible (sans 0/O/1/l/I) */
function generate_temp_password($length = 12) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $pwd = '';
    for ($i = 0; $i < $length; $i++) {
        $pwd .= $chars[random_int(0, strlen($chars) - 1)];
    }
    // Garantir au moins une lettre et un chiffre (règle de changer-mdp.php)
    return $pwd . random_int(2, 9);
}