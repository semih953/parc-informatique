<?php
$page_title = 'Détails Matériel - Parc Informatique DITIB France';
$current_page = 'materiel';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$materiel = null;

if ($id) {
    $materiels = $dataManager->read('materiel');
    foreach ($materiels as $mat) {
        if ($mat['id'] == $id) {
            $materiel = $mat;
            break;
        }
    }
}

if (!$materiel) {
    header('Location: materiel.php');
    exit;
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Détails de l'Équipement</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($materiel['id_equipement']); ?></p>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Informations Générales</h2>
                <div style="display: flex; gap: 1rem;">
                    <a href="modifier-materiel.php?id=<?php echo $materiel['id']; ?>" class="btn btn-primary btn-small">Modifier</a>
                    <a href="materiel.php" class="btn btn-secondary btn-small">Retour</a>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">ID Équipement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['id_equipement'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Société</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['societe'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Type d'Équipement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['type_equipement'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Marque</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['marque'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Modèle</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['modele'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Numéro de Série</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['numero_serie'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Emplacement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['emplacement'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Utilisateur Assigné</div>
                    <div class="detail-value"><?php echo htmlspecialchars($materiel['utilisateur'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Statut</div>
                    <div class="detail-value">
                        <?php
                        $statut = $materiel['statut'] ?? 'actif';
                        $badgeClass = 'badge-success';
                        if ($statut == 'maintenance') $badgeClass = 'badge-warning';
                        elseif ($statut == 'hors-service') $badgeClass = 'badge-danger';
                        elseif ($statut == 'stockage') $badgeClass = 'badge-info';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($statut); ?></span>
                    </div>
                </div>
            </div>

            <?php if (!empty($materiel['notes'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 0.5rem; color: #475569;">Notes</h3>
                <p style="color: #64748b;"><?php echo nl2br(htmlspecialchars($materiel['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($materiel['image']) && file_exists($materiel['image'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 1rem; color: #475569;">Photo de l'équipement</h3>
                <img src="<?php echo htmlspecialchars($materiel['image']); ?>" alt="Photo équipement" style="max-width:400px; max-height:300px; border-radius:8px; border:1px solid #ddd; object-fit:contain;">
            </div>
            <?php endif; ?>

            <?php if (!empty($materiel['date_creation'])): ?>
            <div style="margin-top: 2rem; padding: 1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.875rem; color: #64748b;">
                Ajouté le <?php echo date('d/m/Y à H:i', strtotime($materiel['date_creation'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>