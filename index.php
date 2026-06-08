<?php
$page_title = 'Parc Informatique DITIB France';
$current_page = 'dashboard';
require_once 'dataManager.php';

$totalMateriel = $dataManager->count('materiel');
$totalLogiciels = $dataManager->count('logiciels');
$totalReseau = $dataManager->count('reseau');

$materiels = $dataManager->read('materiel');
$equipementsActifs = count(array_filter($materiels, function($m) {
    return isset($m['statut']) && $m['statut'] === 'actif';
}));

$alertes = count(array_filter($materiels, function($m) {
    return isset($m['statut']) && $m['statut'] === 'maintenance';
}));

$reseaux = $dataManager->read('reseau');
$reseauActif = count(array_filter($reseaux, function($r) {
    return isset($r['statut']) && $r['statut'] === 'actif';
}));

$logiciels = $dataManager->read('logiciels');
$coutTotalLogiciels = 0;
foreach ($logiciels as $log) {
    $coutTotalLogiciels += floatval($log['cout_annuel'] ?? 0);
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Applications</h1>
            <p class="page-subtitle">Gérez votre parc informatique DITIB France</p>
        </div>
    </div>

    <div class="odoo-apps-grid">
        <a href="materiel.php" class="odoo-app-card purple">
            <div class="odoo-app-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="odoo-app-info">
                <h3 class="odoo-app-title">Matériel</h3>
                <p class="odoo-app-desc"><?php echo $totalMateriel; ?> équipements</p>
                <p class="odoo-app-status">
                    <?php echo $equipementsActifs; ?> actifs
                    <?php if ($alertes > 0): ?>
                        • <span style="color: #ffc107;"><?php echo $alertes; ?> alertes</span>
                    <?php endif; ?>
                </p>
            </div>
        </a>

        <a href="logiciels.php" class="odoo-app-card blue">
            <div class="odoo-app-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div class="odoo-app-info">
                <h3 class="odoo-app-title">Logiciels</h3>
                <p class="odoo-app-desc"><?php echo $totalLogiciels; ?> licences</p>
                <p class="odoo-app-status"><?php echo number_format($coutTotalLogiciels, 0, ',', ' '); ?> € / an</p>
            </div>
        </a>

        <a href="reseau.php" class="odoo-app-card green">
            <div class="odoo-app-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
            </div>
            <div class="odoo-app-info">
                <h3 class="odoo-app-title">Réseau</h3>
                <p class="odoo-app-desc"><?php echo $totalReseau; ?> équipements</p>
                <p class="odoo-app-status"><?php echo $reseauActif; ?> opérationnels</p>
            </div>
        </a>

        <a href="ajout-materiel.php" class="odoo-app-card orange">
            <div class="odoo-app-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div class="odoo-app-info">
                <h3 class="odoo-app-title">Ajouter</h3>
                <p class="odoo-app-desc">Nouveau matériel</p>
                <p class="odoo-app-status">Enregistrer un équipement</p>
            </div>
        </a>
    </div>

    <div class="content-card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2 class="card-title">Vue d'ensemble</h2>
        </div>
        <div class="stats-grid" style="padding: 1.5rem;">
            <div class="stat-card purple">
                <div class="stat-label">Total Matériel</div>
                <div class="stat-value"><?php echo $totalMateriel; ?></div>
                <div class="stat-info"><?php echo $equipementsActifs; ?> actifs • <?php echo $alertes; ?> alertes</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-label">Logiciels Installés</div>
                <div class="stat-value"><?php echo $totalLogiciels; ?></div>
                <div class="stat-info"><?php echo number_format($coutTotalLogiciels, 0, ',', ' '); ?> € / an</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Équipements Réseau</div>
                <div class="stat-value"><?php echo $totalReseau; ?></div>
                <div class="stat-info"><?php echo $reseauActif; ?> opérationnels</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Santé du Système</div>
                <div class="stat-value"><?php echo $totalMateriel > 0 ? round(($equipementsActifs / $totalMateriel) * 100) : 100; ?>%</div>
                <div class="stat-info">
                    <?php
                    $sante = $totalMateriel > 0 ? round(($equipementsActifs / $totalMateriel) * 100) : 100;
                    if ($sante >= 90) echo '🟢 Excellent';
                    elseif ($sante >= 70) echo '🟡 Bon';
                    else echo '🔴 À surveiller';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Actions rapides</h2>
        </div>
        <div class="quick-actions-grid">
            <a href="ajout-materiel.php" class="quick-action-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Ajouter du matériel</span>
            </a>
            <a href="ajout-logiciel.php" class="quick-action-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Ajouter un logiciel</span>
            </a>
            <a href="ajout-reseau.php" class="quick-action-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Ajouter équipement réseau</span>
            </a>
            <a href="materiel.php" class="quick-action-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Voir tout le matériel</span>
            </a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>