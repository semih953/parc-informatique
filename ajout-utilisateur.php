<?php
$page_title = 'Ajouter Utilisateur - Parc Informatique DITIB France';
$current_page = 'utilisateurs';
require_once 'dataManager.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'service' => $_POST['service'] ?? '',
        'fonction' => $_POST['fonction'] ?? '',
        'email' => $_POST['email'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];

    $id = $dataManager->add('personnes', $data);
    $message = "✅ Utilisateur ajouté avec succès ! (ID: $id)";
}
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Ajouter un Utilisateur</h1>
        <p class="page-subtitle">Enregistrer une personne pouvant être assignée à un équipement</p>
    </div>

    <?php if ($message): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        <?php echo $message; ?>
        <a href="utilisateurs.php" style="color: #065f46; text-decoration: underline; margin-left: 1rem;">Voir la liste</a>
    </div>
    <?php endif; ?>

    <div class="content-card">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom Complet *</label>
                    <input type="text" name="nom" placeholder="Ex: Jean Dupont" required>
                </div>
                <div class="form-group">
                    <label>Société / Service</label>
                    <select name="service">
                        <option value="">Sélectionner</option>
                        <option value="AHI">AHI</option>
                        <option value="Isra Voyages">Isra Voyages</option>
                        <option value="Cenaze şirketi">Cenaze şirketi</option>
                        <option value="Ditib France">Ditib France</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Fonction / Poste</label>
                    <input type="text" name="fonction" placeholder="Ex: Comptable, Imam, Secrétaire...">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Ex: jean.dupont@ditibfrance.fr">
                </div>
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
            </div>

            <div style="padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="utilisateurs.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>