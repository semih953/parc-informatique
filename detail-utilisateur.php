<?php
$page_title = 'Détails Utilisateur - Parc Informatique DITIB France';
$current_page = 'utilisateurs';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$personne = null;

if ($id) {
    $personnes = $dataManager->read('personnes');
    foreach ($personnes as $p) {
        if ($p['id'] == $id) { $personne = $p; break; }
    }
}

if (!$personne) {
    header('Location: utilisateurs.php');
    exit;
}

$nom = $personne['nom'] ?? '';

$materiels = array_values(array_filter($dataManager->read('materiel'), function($m) use ($nom) {
    return ($m['utilisateur'] ?? '') === $nom && $nom !== '';
}));
$reseaux = array_values(array_filter($dataManager->read('reseau'), function($r) use ($nom) {
    return ($r['utilisateur'] ?? '') === $nom && $nom !== '';
}));
$logiciels = array_values(array_filter($dataManager->read('logiciels'), function($l) use ($nom) {
    return ($l['utilisateur'] ?? '') === $nom && $nom !== '';
}));

function badgeStatut($statut) {
    $statut = $statut ?? 'actif';
    $class = 'badge-success';
    if ($statut === 'maintenance') $class = 'badge-warning';
    elseif ($statut === 'hors-service') $class = 'badge-danger';
    elseif ($statut === 'stockage') $class = 'badge-info';
    return '<span class="badge ' . $class . '">' . htmlspecialchars(ucfirst($statut)) . '</span>';
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Détails de l'Utilisateur</h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($nom); ?></p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Informations Générales</h2>
            <div style="display: flex; gap: 1rem;">
                <a href="modifier-utilisateur.php?id=<?php echo $personne['id']; ?>" class="btn btn-primary btn-small">Modifier</a>
                <a href="utilisateurs.php" class="btn btn-secondary btn-small">Retour</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Nom Complet</div>
                <div class="detail-value"><?php echo htmlspecialchars($personne['nom'] ?? '-'); ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Société / Service</div>
                <div class="detail-value"><?php echo htmlspecialchars($personne['service'] ?? '-'); ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Fonction</div>
                <div class="detail-value"><?php echo htmlspecialchars($personne['fonction'] ?? '-'); ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?php echo htmlspecialchars($personne['email'] ?? '-'); ?></div>
            </div>
        </div>

        <?php if (!empty($personne['notes'])): ?>
        <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
            <h3 style="margin-bottom: 0.5rem; color: #475569;">Notes</h3>
            <p style="color: #64748b;"><?php echo nl2br(htmlspecialchars($personne['notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="content-card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h2 class="card-title">Matériel assigné (<?php echo count($materiels); ?>)</h2>
        </div>
        <?php if (count($materiels) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Type</th><th>Marque / Modèle</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($materiels as $mat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($mat['id_equipement'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($mat['type_equipement'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(trim(($mat['marque'] ?? '') . ' ' . ($mat['modele'] ?? ''))); ?></td>
                        <td><?php echo badgeStatut($mat['statut'] ?? 'actif'); ?></td>
                        <td><a href="detail-materiel.php?id=<?php echo $mat['id']; ?>" class="action-btn action-view">Voir</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 2rem; color: var(--text-light);">Aucun matériel assigné.</p>
        <?php endif; ?>
    </div>

    <div class="content-card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h2 class="card-title">Équipements réseau assignés (<?php echo count($reseaux); ?>)</h2>
        </div>
        <?php if (count($reseaux) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Type</th><th>Marque / Modèle</th><th>Adresse IP</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($reseaux as $res): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($res['id_equipement'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($res['type_equipement'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(trim(($res['marque'] ?? '') . ' ' . ($res['modele'] ?? ''))); ?></td>
                        <td><?php echo htmlspecialchars($res['adresse_ip'] ?? '-'); ?></td>
                        <td><a href="detail-reseau.php?id=<?php echo $res['id']; ?>" class="action-btn action-view">Voir</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 2rem; color: var(--text-light);">Aucun équipement réseau assigné.</p>
        <?php endif; ?>
    </div>

    <div class="content-card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h2 class="card-title">Logiciels assignés (<?php echo count($logiciels); ?>)</h2>
        </div>
        <?php if (count($logiciels) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Nom</th><th>Version</th><th>Éditeur</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($logiciels as $log): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($log['nom'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($log['version'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($log['editeur'] ?? '-'); ?></td>
                        <td><a href="detail-logiciel.php?id=<?php echo $log['id']; ?>" class="action-btn action-view">Voir</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 2rem; color: var(--text-light);">Aucun logiciel assigné.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>