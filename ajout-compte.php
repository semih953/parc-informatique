<?php
$page_title = 'Ajouter Compte - Parc Informatique DITIB France';
$current_page = 'comptes';
require_once 'dataManager.php';
require_once 'cryptoHelper.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom'         => $_POST['nom'] ?? '',
        'service'     => $_POST['service'] ?? '',
        'identifiant' => encryptData($_POST['identifiant'] ?? ''),
        'mot_de_passe'=> encryptData($_POST['mot_de_passe'] ?? ''),
        'url'         => $_POST['url'] ?? '',
        'notes'       => $_POST['notes'] ?? ''
    ];

    $id = $dataManager->add('comptes', $data);
    $message = "✅ Compte ajouté avec succès ! (ID: $id)";
}
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Ajouter un Compte</h1>
        <p class="page-subtitle">Enregistrer un nouvel identifiant de manière sécurisée</p>
    </div>

    <?php if ($message): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        <?php echo $message; ?>
        <a href="comptes.php" style="color: #065f46; text-decoration: underline; margin-left: 1rem;">Voir la liste</a>
    </div>
    <?php endif; ?>

    <div class="content-card">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom du Compte *</label>
                    <input type="text" name="nom" placeholder="Ex: Compte Free, Compte Odoo, Gmail direction..." required>
                </div>
                <div class="form-group">
                    <label>Service / Type</label>
                    <input type="text" name="service" placeholder="Ex: Email, Hébergement, ERP, Banque...">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Identifiant / Email</label>
                    <input type="text" name="identifiant" placeholder="Ex: admin@ditibfrance.fr" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Mot de Passe</label>
                    <div style="position: relative;">
                        <input type="password" name="mot_de_passe" id="mdp_input" placeholder="Mot de passe..." autocomplete="new-password"
                               style="padding-right: 3rem;">
                        <button type="button" onclick="toggleMdp()" id="toggle_btn"
                                style="position:absolute;right:0.5rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;" title="Afficher/Masquer">
                            👁️
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>URL du Service</label>
                <input type="url" name="url" placeholder="Ex: https://www.free.fr/mon-compte">
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Informations supplémentaires, numéro de client, etc."></textarea>
            </div>

            <div style="padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="comptes.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMdp() {
    const input = document.getElementById('mdp_input');
    const btn = document.getElementById('toggle_btn');
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}
</script>

<?php require_once 'footer.php'; ?>