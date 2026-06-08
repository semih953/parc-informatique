<?php
$page_title = 'Réseau - Parc Informatique DITIB France';
$current_page = 'reseau';
require_once 'dataManager.php';

$reseau = $dataManager->read('reseau');
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Gestion du Réseau</h1>
            <p class="page-subtitle">Infrastructure réseau et équipements de connectivité</p>
        </div>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            ✅ Équipement réseau supprimé avec succès !
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card green">
                <div class="stat-label">Total Équipements</div>
                <div class="stat-value"><?php echo count($reseau); ?></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Liste des Équipements Réseau</h2>
                <a href="ajout-reseau.php" class="btn btn-primary btn-small">Ajouter équipement</a>
            </div>
            
            <?php if (count($reseau) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Modèle</th>
                            <th>Adresse IP</th>
                            <th>Emplacement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reseau as $res): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($res['id_equipement'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($res['type_equipement'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(($res['marque'] ?? '') . ' ' . ($res['modele'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($res['adresse_ip'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($res['emplacement'] ?? '-'); ?></td>
                            <td>
                                <?php
                                $statut = $res['statut'] ?? 'actif';
                                $badgeClass = 'badge-success';
                                if ($statut == 'maintenance') $badgeClass = 'badge-warning';
                                elseif ($statut == 'hors-service') $badgeClass = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($statut); ?></span>
                            </td>
                            <td>
                                <a href="detail-reseau.php?id=<?php echo $res['id']; ?>" class="action-btn action-view">Voir</a>
                                <a href="modifier-reseau.php?id=<?php echo $res['id']; ?>" class="action-btn action-edit">Modifier</a>
                                <a href="supprimer-reseau.php?id=<?php echo $res['id']; ?>" class="action-btn action-delete">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 3rem; color: var(--text-medium);">
                Aucun équipement réseau enregistré pour le moment.
            </p>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>