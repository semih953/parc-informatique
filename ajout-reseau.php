<?php
$page_title = 'Ajouter Réseau - Parc Informatique DITIB France';
$current_page = 'ajouter';
require_once 'dataManager.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = $dataManager->handleImageUpload('image');
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
        'notes' => $_POST['notes'] ?? '',
        'image' => $imagePath
    ];

    $id = $dataManager->add('reseau', $data);
    $message = "✅ Équipement réseau ajouté avec succès ! (ID: $id)";
}
require_once 'header.php';
?>

<div class="container">
        <div class="page-header">
            <h1 class="page-title">Ajouter un Équipement Réseau</h1>
            <p class="page-subtitle">Enregistrer un nouvel équipement réseau</p>
        </div>

        <?php if ($message): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <?php echo $message; ?>
            <a href="reseau.php" style="color: #065f46; text-decoration: underline; margin-left: 1rem;">Voir la liste</a>
        </div>
        <?php endif; ?>

        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label>ID Équipement *</label>
                        <input type="text" name="id_equipement" placeholder="Ex: SW-001" required>
                    </div>
                    <div class="form-group">
                        <label>Type d'Équipement *</label>
                        <select name="type_equipement" required>
                            <option value="">Sélectionner</option>
                            <option>Switch</option>
                            <option>Routeur</option>
                            <option>Point d'Accès WiFi</option>
                            <option>Pare-feu</option>
                            <option>Serveur</option>
                            <option>Modem</option>
                            <option>Autre</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Marque *</label>
                        <input type="text" name="marque" placeholder="Ex: Cisco" required>
                    </div>
                    <div class="form-group">
                        <label>Modèle</label>
                        <input type="text" name="modele" placeholder="Ex: SG350-28P">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Adresse IP</label>
                        <input type="text" name="adresse_ip" placeholder="Ex: 192.168.1.10">
                    </div>
                    <div class="form-group">
                        <label>Emplacement</label>
                        <input type="text" name="emplacement" placeholder="Ex: Salle Serveur">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Ports Totaux</label>
                        <input type="number" name="ports_totaux" placeholder="Ex: 28" min="0">
                    </div>
                    <div class="form-group">
                        <label>Ports Utilisés</label>
                        <input type="number" name="ports_utilises" placeholder="Ex: 24" min="0">
                    </div>
                </div>

                <div class="form-group" style="padding: 0 1.5rem;">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif">Actif</option>
                        <option value="maintenance">En maintenance</option>
                        <option value="hors-service">Hors service</option>
                    </select>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Informations supplémentaires..."></textarea>
                </div>

                <div class="form-group" style="padding: 1rem 1.5rem 0;">
                    <label>Image de l'équipement réseau</label>
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
                    <a href="reseau.php" class="btn btn-secondary">Annuler</a>
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