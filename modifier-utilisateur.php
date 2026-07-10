<?php
$page_title = 'Modifier Utilisateur - Parc Informatique DITIB France';
$current_page = 'utilisateurs';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$personne = null;
$message = '';

if ($id) {
    $personnes = $dataManager->read('personnes');
    foreach ($personnes as $p) {
        if ($p['id'] == $id) { $personne = $p; break; }
    }
}

if (!$personne) {
    header('Location: utilisateurs.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancienNom = $personne['nom'] ?? '';

    $data = [
        'nom' => $_POST['nom'] ?? '',
        'service' => $_POST['service'] ?? '',
        'fonction' => $_POST['fonction'] ?? '',
        'email' => $_POST['email'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];

    $dataManager->update('personnes', $id, $data);

    // Si le nom a changé, on met à jour toutes les assignations existantes
    // pour ne pas "perdre" le lien avec le matériel / réseau / logiciels déjà assignés
    if ($ancienNom !== '' && $ancienNom !== $data['nom']) {
        foreach (['materiel', 'reseau', 'logiciels'] as $type) {
            $items = $dataManager->read($type);
            foreach ($items as $item) {
                if (($item['utilisateur'] ?? '') === $ancienNom) {
                    $dataManager->update($type, $item['id'], ['utilisateur' => $data['nom']]);
                }
            }
        }
    }

    $message = "✅ Utilisateur modifié avec succès !";

    $personnes = $dataManager->read('personnes');
    foreach ($personnes as $p) {
        if ($p['id'] == $id) { $personne = $p; break; }
    }
}
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Modifier l'Utilisateur</h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($personne['nom'] ?? ''); ?></p>
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
                    <label>Nom Complet *</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($personne['nom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Société / Service</label>
                    <select name="service">
                        <option value="">Sélectionner</option>
                        <option value="AHI" <?php echo ($personne['service'] ?? '') == 'AHI' ? 'selected' : ''; ?>>AHI</option>
                        <option value="Isra Voyages" <?php echo ($personne['service'] ?? '') == 'Isra Voyages' ? 'selected' : ''; ?>>Isra Voyages</option>
                        <option value="Cenaze şirketi" <?php echo ($personne['service'] ?? '') == 'Cenaze şirketi' ? 'selected' : ''; ?>>Cenaze şirketi</option>
                        <option value="Ditib France" <?php echo ($personne['service'] ?? '') == 'Ditib France' ? 'selected' : ''; ?>>Ditib France</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Fonction / Poste</label>
                    <input type="text" name="fonction" value="<?php echo htmlspecialchars($personne['fonction'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($personne['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group" style="padding: 0 1.5rem 1.5rem;">
                <label>Notes</label>
                <textarea name="notes" rows="3"><?php echo htmlspecialchars($personne['notes'] ?? ''); ?></textarea>
            </div>

            <div style="padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="detail-utilisateur.php?id=<?php echo $personne['id']; ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>