<?php
$page_title = 'Comptes & Identifiants - Parc Informatique DITIB France';
$current_page = 'comptes';
require_once 'dataManager.php';
require_once 'cryptoHelper.php';

$comptes = $dataManager->read('comptes');
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Comptes &amp; Identifiants</h1>
        <p class="page-subtitle">Gestion sécurisée des accès et mots de passe</p>
    </div>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        ✅ Compte supprimé avec succès !
    </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card" style="border-left: 3px solid #8b5cf6;">
            <div class="stat-label">Total Comptes</div>
            <div class="stat-value"><?php echo count($comptes); ?></div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">🔐 Liste des Comptes</h2>
            <a href="ajout-compte.php" class="btn btn-primary btn-small">Ajouter un compte</a>
        </div>

        <?php if (count($comptes) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nom du Compte</th>
                        <th>Service / Type</th>
                        <th>Identifiant</th>
                        <th>Mot de Passe</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comptes as $compte): ?>
                    <?php
                        $identifiant = decryptData($compte['identifiant'] ?? '');
                        $masqueId = !empty($identifiant)
                            ? substr($identifiant, 0, 3) . str_repeat('●', min(6, strlen($identifiant) - 3))
                            : '-';
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($compte['nom'] ?? 'N/A'); ?></strong></td>
                        <td>
                            <?php if (!empty($compte['service'])): ?>
                            <span class="badge" style="background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd;">
                                <?php echo htmlspecialchars($compte['service']); ?>
                            </span>
                            <?php else: echo '-'; endif; ?>
                        </td>
                        <td style="font-family: monospace; color: #64748b;"><?php echo htmlspecialchars($masqueId); ?></td>
                        <td style="font-family: monospace; letter-spacing: 2px; color: #64748b;">●●●●●●●●</td>
                        <td>
                            <?php if (!empty($compte['url'])): ?>
                            <a href="<?php echo htmlspecialchars($compte['url']); ?>" target="_blank"
                               style="color: var(--accent-color); font-size: 12px;">🔗 Ouvrir</a>
                            <?php else: echo '-'; endif; ?>
                        </td>
                        <td>
                            <a href="detail-compte.php?id=<?php echo $compte['id']; ?>" class="action-btn action-view">Voir</a>
                            <a href="modifier-compte.php?id=<?php echo $compte['id']; ?>" class="action-btn action-edit">Modifier</a>
                            <a href="supprimer-compte.php?id=<?php echo $compte['id']; ?>" class="action-btn action-delete">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 3rem; color: var(--text-light);">
            Aucun compte enregistré. <a href="ajout-compte.php" style="color: var(--primary-color);">Ajoutez-en un !</a>
        </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>