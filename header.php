<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Parc Informatique DITIB France'; ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <!-- Sidebar Odoo Style -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <img src="logoblanc.png" alt="Logo DITIB France">
            </a>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" <?php echo ($current_page ?? '') == 'dashboard' ? 'class="active"' : ''; ?>>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Dashboard
            </a>
            <a href="materiel.php" <?php echo ($current_page ?? '') == 'materiel' ? 'class="active"' : ''; ?>>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Matériel
            </a>
            <a href="logiciels.php" <?php echo ($current_page ?? '') == 'logiciels' ? 'class="active"' : ''; ?>>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Logiciels
            </a>
            <a href="reseau.php" <?php echo ($current_page ?? '') == 'reseau' ? 'class="active"' : ''; ?>>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                Réseau
            </a>
            <a href="ajout-materiel.php" <?php echo ($current_page ?? '') == 'ajouter' ? 'class="active"' : ''; ?>>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="changer-mdp.php" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0.65rem 1rem; margin-bottom: 0.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; border: 1px solid rgba(255,255,255,0.2); border-radius: 4px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Mot de passe
            </a>
            <a href="logout.php" class="btn-logout">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Déconnexion
            </a>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-breadcrumb">
                <span>DITIB France</span>
                <span> / </span>
                <span class="current-page"><?php echo $page_title ?? 'Dashboard'; ?></span>
            </div>
        </div>