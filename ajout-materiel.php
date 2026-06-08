<?php
$page_title = 'Ajouter Matériel - Parc Informatique DITIB France';
$current_page = 'ajouter';
require_once 'dataManager.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $dataManager->handleImageUpload('image');
    $data = [
        'id_equipement' => $_POST['id_equipement'] ?? '',
        'type_equipement' => $_POST['type_equipement'] ?? '',
        'societe' => $_POST['societe'] ?? '',
        'marque' => $_POST['marque'] ?? '',
        'modele' => $_POST['modele'] ?? '',
        'numero_serie' => $_POST['numero_serie'] ?? '',
        'emplacement' => $_POST['emplacement'] ?? '',
        'utilisateur' => $_POST['utilisateur'] ?? '',
        'statut' => $_POST['statut'] ?? 'actif',
        'notes' => $_POST['notes'] ?? '',
        'image' => $imagePath
    ];

    $id = $dataManager->add('materiel', $data);
    $message = "✅ Matériel ajouté avec succès ! (ID: $id)";
}

require_once 'header.php';
?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Ajouter du Matériel</h1>
            <p class="page-subtitle">Enregistrer un nouvel équipement</p>
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
                        <input type="text" name="id_equipement" placeholder="Ex: PC-001" required>
                    </div>
                    <div class="form-group">
                        <label>Société *</label>
                        <select name="societe" required>
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
                        <label>Type d'Équipement *</label>
                        <select name="type_equipement" required>
                            <option value="">Sélectionner</option>
                            <option>Ordinateur Portable</option>
                            <option>Ordinateur Fixe</option>
                            <option>Imprimante</option>
                            <option>Écran</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Marque *</label>
                        <input type="text" name="marque" placeholder="Ex: Dell" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Modèle</label>
                        <input type="text" name="modele" placeholder="Ex: Latitude 5520">
                    </div>
                    <div class="form-group">
                        <label>Numéro de Série</label>
                        <input type="text" name="numero_serie" placeholder="Ex: ABC123">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Emplacement</label>
                        <input type="text" name="emplacement" placeholder="Ex: Bureau Direction">
                    </div>
                    <div class="form-group">
                        <label>Utilisateur Assigné</label>
                        <input type="text" name="utilisateur" placeholder="Ex: Jean Dupont">
                    </div>
                </div>

                <div class="form-group" style="padding: 0 1.5rem;">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif">Actif</option>
                        <option value="maintenance">En maintenance</option>
                        <option value="stockage">En stockage</option>
                        <option value="hors-service">Hors service</option>
                    </select>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Image de l'équipement</label>
                    <div class="image-upload-zone" id="uploadZone">
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
                        <div class="upload-placeholder" onclick="document.getElementById('imageInput').click()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48" style="color:#aaa; margin-bottom:0.75rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p style="color:#666; margin:0; font-size:14px;">Cliquer pour choisir une image</p>
                            <p style="color:#aaa; margin:0.25rem 0 0; font-size:12px;">JPG, PNG, GIF, WEBP — max 5MB</p>
                        </div>
                        <img id="imagePreview" src="" alt="Aperçu" style="display:none; max-width:100%; max-height:200px; border-radius:8px; margin-top:0.5rem;">
                    </div>
                </div>

                <div style="margin-top: 2rem; padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="materiel.php" class="btn btn-secondary">Annuler</a>
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