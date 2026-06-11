<?php
$page_title = 'Supprimer Compte - Parc Informatique DITIB France';
$current_page = 'comptes';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: comptes.php');
    exit;
}

$comptes = $dataManager->read('comptes');
$compte = null;
foreach ($comptes as $c) {
    if ($c['id'] == $id) {
        $compte = $c;
        break;
    }
}

if (!$compte) {
    header('Location: comptes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer'])) {
    $dataManager->delete('comptes', $id);
    header('Location: comptes.php?deleted=1');
    exit;
}
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Supprimer le Compte</h1>
        <p class="page-subtitle">Cette action est irréversible</p>
    </div>

    <div class="content-card">
        <div style="background: #fee2e2; border: 2px solid #ef4444; padding: 2rem; border-radius: 12px; text-align: center;">
            <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>

            <h2 style="color: #991b1b; margin-bottom: 1rem; font-size: 1.5rem;">
                Êtes-vous sûr de vouloir supprimer ce compte ?
            </h2>

            <div style="background: white; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; text-align: left;">
                <p style="margin-bottom: 0.5rem;"><strong>Nom :</strong> <?php echo htmlspecialchars($compte['nom'] ?? '-'); ?></p>
                <p style="margin-bottom: 0;"><strong>Service :</strong> <?php echo htmlspecialchars($compte['service'] ?? '-'); ?></p>
            </div>

            <p style="color: #991b1b; margin-bottom: 2rem; font-size: 0.95rem;">
                ⚠️ Cette action est <strong>définitive</strong> et <strong>irréversible</strong>.
            </p>

            <form method="POST" style="display: flex; gap: 1rem; justify-content: center;">
                <button type="submit" name="confirmer" class="btn"
                        style="background: #ef4444; color: white; border: none; padding: 0.75rem 2rem; font-size: 1rem; cursor: pointer; border-radius: 8px; font-weight: 600;">
                    Oui, supprimer définitivement
                </button>
                <a href="comptes.php" class="btn btn-secondary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                    Non, annuler
                </a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>