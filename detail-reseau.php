<?php
$page_title = 'Détails Réseau - Parc Informatique DITIB France';
$current_page = 'reseau';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$equipement = null;

if ($id) {
    $equipements = $dataManager->read('reseau');
    foreach ($equipements as $eq) {
        if ($eq['id'] == $id) {
            $equipement = $eq;
            break;
        }
    }
}

if (!$equipement) {
    header('Location: reseau.php');
    exit;
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Détails Équipement Réseau</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($equipement['id_equipement']); ?></p>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Informations Générales</h2>
                <div style="display: flex; gap: 1rem;">
                    <a href="modifier-reseau.php?id=<?php echo $equipement['id']; ?>" class="btn btn-primary btn-small">Modifier</a>
                    <a href="reseau.php" class="btn btn-secondary btn-small">Retour</a>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">ID Équipement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['id_equipement'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Type d'Équipement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['type_equipement'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Marque</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['marque'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Modèle</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['modele'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Adresse IP</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['adresse_ip'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Emplacement</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['emplacement'] ?? '-'); ?></div>
                </div>
                <?php if (!empty($equipement['ports_totaux'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Ports Totaux</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['ports_totaux']); ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($equipement['ports_utilises'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Ports Utilisés</div>
                    <div class="detail-value"><?php echo htmlspecialchars($equipement['ports_utilises']); ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-item">
                    <div class="detail-label">Statut</div>
                    <div class="detail-value">
                        <?php
                        $statut = $equipement['statut'] ?? 'actif';
                        $badgeClass = 'badge-success';
                        if ($statut == 'maintenance') $badgeClass = 'badge-warning';
                        elseif ($statut == 'hors-service') $badgeClass = 'badge-danger';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($statut); ?></span>
                    </div>
                </div>
            </div>

            <?php if (!empty($equipement['notes'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 0.5rem; color: #475569;">Notes</h3>
                <p style="color: #64748b;"><?php echo nl2br(htmlspecialchars($equipement['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($equipement['image']) && file_exists($equipement['image'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 1rem; color: #475569;">Photo de l'équipement</h3>
                <img src="<?php echo htmlspecialchars($equipement['image']); ?>" alt="Photo équipement réseau" style="max-width:400px; max-height:300px; border-radius:8px; border:1px solid #ddd; object-fit:contain;">
            </div>
            <?php endif; ?>

            <?php if (!empty($equipement['date_creation'])): ?>
            <div style="margin-top: 2rem; padding: 1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.875rem; color: #64748b;">
                Ajouté le <?php echo date('d/m/Y à H:i', strtotime($equipement['date_creation'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>