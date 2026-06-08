<?php
$page_title = 'Modifier Réseau - Parc Informatique DITIB France';
$current_page = 'reseau';
require_once 'dataManager.php';

$id = $_GET['id'] ?? null;
$equipement = null;
$message = '';

if ($id) {
    $equipements = $dataManager->read('reseau');
    foreach ($equipements as $eq) {
        if ($eq['id'] == $id) {
            $equipement = $eq;
            break;
        }
    }
}

if (!$equipement) {
    header('Location: reseau.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id_equipement' => $_POST['id_equipement'] ?? '',
        'type_equipement' => $_POST['type_equipement'] ?? '',
        'marque' => $_POST['marque'] ?? '',
        'modele' => $_POST['modele'] ?? '',
        'adresse_ip' => $_POST['adresse_ip'] ?? '',
        'emplacement' => $_POST['emplacement'] ?? '',
        'ports_totaux' => $_POST['ports_totaux'] ?? 0,
        'ports_utilises' => $_POST['ports_utilises'] ?? 0,
        'statut' => $_POST['statut'] ?? 'actif',
        'notes' => $_POST['notes'] ?? ''
    ];

    $newImage = $dataManager->handleImageUpload('image');
    if ($newImage) {
        $data['image'] = $newImage;
    }

    $dataManager->update('reseau', $id, $data);
    $message = "✅ Équipement réseau modifié avec succès !";

    $equipements = $dataManager->read('reseau');
    foreach ($equipements as $eq) {
        if ($eq['id'] == $id) {
            $equipement = $eq;
            break;
        }
    }
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Modifier l'Équipement Réseau</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($equipement['id_equipement']); ?></p>
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
                        <label>ID Équipement *</label>
                        <input type="text" name="id_equipement" value="<?php echo htmlspecialchars($equipement['id_equipement'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Type d'Équipement *</label>
                        <select name="type_equipement" required>
                            <option value="">Sélectionner</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Switch' ? 'selected' : ''; ?>>Switch</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Routeur' ? 'selected' : ''; ?>>Routeur</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == "Point d'Accès WiFi" ? 'selected' : ''; ?>>Point d'Accès WiFi</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Pare-feu' ? 'selected' : ''; ?>>Pare-feu</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Serveur' ? 'selected' : ''; ?>>Serveur</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Modem' ? 'selected' : ''; ?>>Modem</option>
                            <option <?php echo ($equipement['type_equipement'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Marque *</label>
                        <input type="text" name="marque" value="<?php echo htmlspecialchars($equipement['marque'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Modèle</label>
                        <input type="text" name="modele" value="<?php echo htmlspecialchars($equipement['modele'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Adresse IP</label>
                        <input type="text" name="adresse_ip" value="<?php echo htmlspecialchars($equipement['adresse_ip'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Emplacement</label>
                        <input type="text" name="emplacement" value="<?php echo htmlspecialchars($equipement['emplacement'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Ports Totaux</label>
                        <input type="number" name="ports_totaux" value="<?php echo htmlspecialchars($equipement['ports_totaux'] ?? '0'); ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label>Ports Utilisés</label>
                        <input type="number" name="ports_utilises" value="<?php echo htmlspecialchars($equipement['ports_utilises'] ?? '0'); ?>" min="0">
                    </div>
                </div>

                <div class="form-group" style="padding: 0 1.5rem;">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif" <?php echo ($equipement['statut'] ?? 'actif') == 'actif' ? 'selected' : ''; ?>>Actif</option>
                        <option value="maintenance" <?php echo ($equipement['statut'] ?? '') == 'maintenance' ? 'selected' : ''; ?>>En maintenance</option>
                        <option value="hors-service" <?php echo ($equipement['statut'] ?? '') == 'hors-service' ? 'selected' : ''; ?>>Hors service</option>
                    </select>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?php echo htmlspecialchars($equipement['notes'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Image de l'équipement réseau</label>
                    <?php if (!empty($equipement['image']) && file_exists($equipement['image'])): ?>
                    <div style="margin-bottom:0.75rem;">
                        <img src="<?php echo htmlspecialchars($equipement['image']); ?>" alt="Image actuelle" style="max-width:200px; max-height:150px; border-radius:8px; border:1px solid #ddd;">
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
                    <a href="detail-reseau.php?id=<?php echo $equipement['id']; ?>" class="btn btn-secondary">Annuler</a>
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