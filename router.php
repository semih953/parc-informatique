<?php
/**
 * router.php - Pare-feu de fichiers pour le serveur PHP intégré
 *
 * Lancer le site avec :
 *   php -S localhost:8000 router.php
 *
 * Bloque l'accès direct aux fichiers sensibles (users.json, data/, .git, etc.)
 * qui seraient sinon téléchargeables par n'importe qui.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ===== BLOCAGES =====
$blocked =
    // Tous les fichiers JSON (users.json, data/*.json, etc.)
    preg_match('#\.json$#i', $path)
    // Le dossier data/ en entier
    || preg_match('#^/data(/|$)#i', $path)
    // Fichiers et dossiers cachés (.git, .gitignore, .env, .htaccess...)
    || preg_match('#/\.#', $path)
    // Fichiers de log et de sauvegarde
    || preg_match('#\.(log|bak|old|backup|sql)$#i', $path)
    // Exécution de PHP dans uploads/ (au cas où un fichier malveillant y serait déposé)
    || preg_match('#^/uploads/.*\.php#i', $path);

if ($blocked) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403 - Accès interdit');
}

// Servir les fichiers existants normalement
return false;