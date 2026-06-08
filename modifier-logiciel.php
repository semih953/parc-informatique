<?php
$page_title = 'Modifier Logiciel - Parc Informatique DITIB France';
$current_page = 'logiciels';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$logiciel = null;
$message = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'version' => $_POST['version'] ?? '',
        'editeur' => $_POST['editeur'] ?? '',
        'categorie' => $_POST['categorie'] ?? '',
        'nombre_licences' => $_POST['nombre_licences'] ?? 1,
        'cout_annuel' => $_POST['cout_annuel'] ?? 0,
        'date_expiration' => $_POST['date_expiration'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];

    $newImage = $dataManager->handleImageUpload('image');
    if ($newImage) {
        $data['image'] = $newImage;
    }

    $dataManager->update('logiciels', $id, $data);
    $message = "✅ Logiciel modifié avec succès !";

    $logiciels = $dataManager->read('logiciels');
    foreach ($logiciels as $log) {
        if ($log['id'] == $id) {
            $logiciel = $log;
            break;
        }
    }
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Modifier le Logiciel</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($logiciel['nom']); ?></p>
        </div>

        <?php if ($message): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom du Logiciel *</label>
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($logiciel['nom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Version</label>
                        <input type="text" name="version" value="<?php echo htmlspecialchars($logiciel['version'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Éditeur</label>
                        <input type="text" name="editeur" value="<?php echo htmlspecialchars($logiciel['editeur'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="categorie">
                            <option value="">Sélectionner</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Bureautique' ? 'selected' : ''; ?>>Bureautique</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Design/Création' ? 'selected' : ''; ?>>Design/Création</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Développement' ? 'selected' : ''; ?>>Développement</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Sécurité' ? 'selected' : ''; ?>>Sécurité</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Système' ? 'selected' : ''; ?>>Système</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Communication' ? 'selected' : ''; ?>>Communication</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Base de données' ? 'selected' : ''; ?>>Base de données</option>
                            <option <?php echo ($logiciel['categorie'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre de Licences</label>
                        <input type="number" name="nombre_licences" value="<?php echo htmlspecialchars($logiciel['nombre_licences'] ?? '1'); ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label>Coût Annuel (€)</label>
                        <input type="number" name="cout_annuel" value="<?php echo htmlspecialchars($logiciel['cout_annuel'] ?? '0'); ?>" step="0.01" min="0">
                    </div>
                </div>

                <div class="form-group" style="padding: 0 1.5rem;">
                    <label>Date d'Expiration</label>
                    <input type="date" name="date_expiration" value="<?php echo htmlspecialchars($logiciel['date_expiration'] ?? ''); ?>">
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?php echo htmlspecialchars($logiciel['notes'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Image / Logo du logiciel</label>
                    <?php if (!empty($logiciel['image']) && file_exists($logiciel['image'])): ?>
                    <div style="margin-bottom:0.75rem;">
                        <img src="<?php echo htmlspecialchars($logiciel['image']); ?>" alt="Image actuelle" style="max-width:200px; max-height:150px; border-radius:8px; border:1px solid #ddd;">
                        <p style="font-size:12px; color:#888; margin-top:0.25rem;">Image actuelle</p>
                    </div>
                    <?php endif; ?>
                    <div class="image-upload-zone">
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
                        <div class="upload-placeholder" onclick="document.getElementById('imageInput').click()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="40" height="40" style="color:#aaa; margin-bottom:0.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p style="color:#666; margin:0; font-size:14px;">Changer l'image (optionnel)</p>
                            <p style="color:#aaa; margin:0.25rem 0 0; font-size:12px;">JPG, PNG, GIF, WEBP — max 5MB</p>
                        </div>
                        <img id="imagePreview" src="" alt="Aperçu" style="display:none; max-width:100%; max-height:200px; border-radius:8px; margin-top:0.5rem;">
                    </div>
                </div>

                <div style="margin-top: 2rem; padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="detail-logiciel.php?id=<?php echo $logiciel['id']; ?>" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('imagePreview');
            preview.src = ev.target.result;
            preview.style.display = 'block';
            document.querySelector('.upload-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once 'footer.php'; ?>