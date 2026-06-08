<?php
$page_title = 'Logiciels - Parc Informatique DITIB France';
$current_page = 'logiciels';
require_once 'dataManager.php';

$logiciels = $dataManager->read('logiciels');
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Gestion des Logiciels</h1>
            <p class="page-subtitle">Suivi des licences et installations logicielles</p>
        </div>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            ✅ Logiciel supprimé avec succès !
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Total Logiciels</div>
                <div class="stat-value"><?php echo count($logiciels); ?></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Liste des Logiciels</h2>
                <a href="ajout-logiciel.php" class="btn btn-primary btn-small">Ajouter un logiciel</a>
            </div>
            
            <?php if (count($logiciels) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Version</th>
                            <th>Éditeur</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logiciels as $log): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($log['nom'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($log['version'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['editeur'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['categorie'] ?? '-'); ?></td>
                            <td>
                                <a href="detail-logiciel.php?id=<?php echo $log['id']; ?>" class="action-btn action-view">Voir</a>
                                <a href="modifier-logiciel.php?id=<?php echo $log['id']; ?>" class="action-btn action-edit">Modifier</a>
                                <a href="supprimer-logiciel.php?id=<?php echo $log['id']; ?>" class="action-btn action-delete">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 3rem; color: var(--text-medium);">
                Aucun logiciel enregistré pour le moment.
            </p>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>