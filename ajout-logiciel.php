<?php
$page_title = 'Ajouter Logiciel - Parc Informatique DITIB France';
$current_page = 'ajouter';
require_once 'dataManager.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $dataManager->handleImageUpload('image');
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'version' => $_POST['version'] ?? '',
        'editeur' => $_POST['editeur'] ?? '',
        'categorie' => $_POST['categorie'] ?? '',
        'nombre_licences' => $_POST['nombre_licences'] ?? 1,
        'cout_annuel' => $_POST['cout_annuel'] ?? 0,
        'date_expiration' => $_POST['date_expiration'] ?? '',
        'notes' => $_POST['notes'] ?? '',
        'image' => $imagePath
    ];

    $id = $dataManager->add('logiciels', $data);
    $message = "✅ Logiciel ajouté avec succès ! (ID: $id)";
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Ajouter un Logiciel</h1>
            <p class="page-subtitle">Enregistrer une nouvelle licence logicielle</p>
        </div>

        <?php if ($message): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <?php echo $message; ?>
            <a href="logiciels.php" style="color: #065f46; text-decoration: underline; margin-left: 1rem;">Voir la liste</a>
        </div>
        <?php endif; ?>

        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom du Logiciel *</label>
                        <input type="text" name="nom" placeholder="Ex: Microsoft Office" required>
                    </div>
                    <div class="form-group">
                        <label>Version</label>
                        <input type="text" name="version" placeholder="Ex: 2024">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Éditeur</label>
                        <input type="text" name="editeur" placeholder="Ex: Microsoft">
                    </div>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="categorie">
                            <option value="">Sélectionner</option>
                            <option>Bureautique</option>
                            <option>Design/Création</option>
                            <option>Développement</option>
                            <option>Sécurité</option>
                            <option>Système</option>
                            <option>Communication</option>
                            <option>Base de données</option>
                            <option>Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre de Licences</label>
                        <input type="number" name="nombre_licences" placeholder="Ex: 50" value="1" min="1">
                    </div>
                    <div class="form-group">
                        <label>Coût Annuel (€)</label>
                        <input type="number" name="cout_annuel" placeholder="Ex: 6000" step="0.01" min="0">
                    </div>
                </div>

                <div class="form-group" style="padding: 0 1.5rem;">
                    <label>Date d'Expiration</label>
                    <input type="date" name="date_expiration">
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Image / Logo du logiciel</label>
                    <div class="image-upload-zone">
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
                    <a href="logiciels.php" class="btn btn-secondary">Annuler</a>
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