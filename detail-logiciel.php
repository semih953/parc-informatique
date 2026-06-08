<?php
$page_title = 'Détails Logiciel - Parc Informatique DITIB France';
$current_page = 'logiciels';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$logiciel = null;

if ($id) {
    $logiciels = $dataManager->read('logiciels');
    foreach ($logiciels as $log) {
        if ($log['id'] == $id) {
            $logiciel = $log;
            break;
        }
    }
}

if (!$logiciel) {
    header('Location: logiciels.php');
    exit;
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Détails du Logiciel</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($logiciel['nom']); ?></p>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Informations Générales</h2>
                <div style="display: flex; gap: 1rem;">
                    <a href="modifier-logiciel.php?id=<?php echo $logiciel['id']; ?>" class="btn btn-primary btn-small">Modifier</a>
                    <a href="logiciels.php" class="btn btn-secondary btn-small">Retour</a>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nom du Logiciel</div>
                    <div class="detail-value"><?php echo htmlspecialchars($logiciel['nom'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Version</div>
                    <div class="detail-value"><?php echo htmlspecialchars($logiciel['version'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Éditeur</div>
                    <div class="detail-value"><?php echo htmlspecialchars($logiciel['editeur'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Catégorie</div>
                    <div class="detail-value"><?php echo htmlspecialchars($logiciel['categorie'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nombre de Licences</div>
                    <div class="detail-value"><?php echo htmlspecialchars($logiciel['nombre_licences'] ?? '0'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Coût Annuel</div>
                    <div class="detail-value"><?php echo number_format($logiciel['cout_annuel'] ?? 0, 2, ',', ' '); ?> €</div>
                </div>
                <?php if (!empty($logiciel['date_expiration'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Date d'Expiration</div>
                    <div class="detail-value"><?php echo date('d/m/Y', strtotime($logiciel['date_expiration'])); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($logiciel['notes'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 0.5rem; color: #475569;">Notes</h3>
                <p style="color: #64748b;"><?php echo nl2br(htmlspecialchars($logiciel['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($logiciel['image']) && file_exists($logiciel['image'])): ?>
            <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
                <h3 style="margin-bottom: 1rem; color: #475569;">Image / Logo</h3>
                <img src="<?php echo htmlspecialchars($logiciel['image']); ?>" alt="Logo logiciel" style="max-width:400px; max-height:300px; border-radius:8px; border:1px solid #ddd; object-fit:contain;">
            </div>
            <?php endif; ?>

            <?php if (!empty($logiciel['date_creation'])): ?>
            <div style="margin-top: 2rem; padding: 1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.875rem; color: #64748b;">
                Ajouté le <?php echo date('d/m/Y à H:i', strtotime($logiciel['date_creation'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>