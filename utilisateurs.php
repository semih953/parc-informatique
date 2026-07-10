<?php
$page_title = 'Utilisateurs - Parc Informatique DITIB France';
$current_page = 'utilisateurs';
require_once 'dataManager.php';

$personnes = $dataManager->read('personnes');
$materiels = $dataManager->read('materiel');
$reseaux   = $dataManager->read('reseau');
$logiciels = $dataManager->read('logiciels');

function compterAssignations($nom, $materiels, $reseaux, $logiciels) {
    if (empty($nom)) return 0;
    $count = 0;
    foreach ($materiels as $m) { if (($m['utilisateur'] ?? '') === $nom) $count++; }
    foreach ($reseaux as $r) { if (($r['utilisateur'] ?? '') === $nom) $count++; }
    foreach ($logiciels as $l) { if (($l['utilisateur'] ?? '') === $nom) $count++; }
    return $count;
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Gestion des Utilisateurs</h1>
        <p class="page-subtitle">Personnes pouvant être assignées à un matériel, un équipement réseau ou un logiciel</p>
    </div>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        ✅ Utilisateur supprimé avec succès !
    </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card purple">
            <div class="stat-label">Total Utilisateurs</div>
            <div class="stat-value"><?php echo count($personnes); ?></div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Liste des Utilisateurs</h2>
            <a href="ajout-utilisateur.php" class="btn btn-primary btn-small">Ajouter un utilisateur</a>
        </div>

        <?php if (count($personnes) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Société / Service</th>
                        <th>Fonction</th>
                        <th>Équipements assignés</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personnes as $p): ?>
                    <?php $nb = compterAssignations($p['nom'] ?? '', $materiels, $reseaux, $logiciels); ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($p['nom'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['service'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($p['fonction'] ?? '-'); ?></td>
                        <td>
                            <?php if ($nb > 0): ?>
                            <span class="badge badge-info"><?php echo $nb; ?> élément(s)</span>
                            <?php else: ?>
                            <span style="color: var(--text-light);">Aucun</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="detail-utilisateur.php?id=<?php echo $p['id']; ?>" class="action-btn action-view">Voir</a>
                            <a href="modifier-utilisateur.php?id=<?php echo $p['id']; ?>" class="action-btn action-edit">Modifier</a>
                            <a href="supprimer-utilisateur.php?id=<?php echo $p['id']; ?>" class="action-btn action-delete">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 3rem; color: var(--text-light);">
            Aucun utilisateur enregistré. <a href="ajout-utilisateur.php" style="color: var(--primary-color);">Ajoutez-en un !</a>
        </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>