<?php
$page_title = 'Modifier Compte - Parc Informatique DITIB France';
$current_page = 'comptes';
require_once 'dataManager.php';
require_once 'cryptoHelper.php';

$id = $_GET['id'] ?? null;
$compte = null;
$message = '';

if ($id) {
    $comptes = $dataManager->read('comptes');
    foreach ($comptes as $c) {
        if ($c['id'] == $id) {
            $compte = $c;
            break;
        }
    }
}

if (!$compte) {
    header('Location: comptes.php');
    exit;
}

$identifiant_clair   = decryptData($compte['identifiant'] ?? '');
$mot_de_passe_clair  = decryptData($compte['mot_de_passe'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom'          => $_POST['nom'] ?? '',
        'service'      => $_POST['service'] ?? '',
        'identifiant'  => encryptData($_POST['identifiant'] ?? ''),
        'mot_de_passe' => encryptData($_POST['mot_de_passe'] ?? ''),
        'url'          => $_POST['url'] ?? '',
        'notes'        => $_POST['notes'] ?? ''
    ];

    $dataManager->update('comptes', $id, $data);
    $message = "✅ Compte modifié avec succès !";

    $comptes = $dataManager->read('comptes');
    foreach ($comptes as $c) {
        if ($c['id'] == $id) {
            $compte = $c;
            break;
        }
    }
    $identifiant_clair  = decryptData($compte['identifiant'] ?? '');
    $mot_de_passe_clair = decryptData($compte['mot_de_passe'] ?? '');
}
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Modifier le Compte</h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($compte['nom']); ?></p>
    </div>

    <?php if ($message): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <div class="content-card">
        <form method="POST">
                <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom du Compte *</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($compte['nom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Service / Type</label>
                    <input type="text" name="service" value="<?php echo htmlspecialchars($compte['service'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Identifiant / Email</label>
                    <input type="text" name="identifiant"
                           value="<?php echo htmlspecialchars($identifiant_clair); ?>" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Mot de Passe</label>
                    <div style="position: relative;">
                        <input type="password" name="mot_de_passe" id="mdp_input"
                               value="<?php echo htmlspecialchars($mot_de_passe_clair); ?>"
                               autocomplete="new-password" style="padding-right: 3rem;">
                        <button type="button" onclick="toggleMdp()" id="toggle_btn"
                                style="position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;">
                            👁️
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>URL du Service</label>
                <input type="url" name="url" value="<?php echo htmlspecialchars($compte['url'] ?? ''); ?>">
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>Notes</label>
                <textarea name="notes" rows="3"><?php echo htmlspecialchars($compte['notes'] ?? ''); ?></textarea>
            </div>

            <div style="padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="detail-compte.php?id=<?php echo $compte['id']; ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMdp() {
    const input = document.getElementById('mdp_input');
    const btn = document.getElementById('toggle_btn');
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁️' : '🙈';
}
</script>

<?php require_once 'footer.php'; ?>