<?php
$page_title = 'Matériel - Parc Informatique DITIB France';
$current_page = 'materiel';
require_once 'dataManager.php';
require_once 'header.php';

$materiels = $dataManager->read('materiel');

// Récupérer les filtres
$filtre_type    = $_GET['type'] ?? '';
$filtre_societe = $_GET['societe'] ?? '';
$filtre_statut  = $_GET['statut'] ?? '';

// Extraire les types uniques depuis les données
$types_disponibles = array_unique(array_filter(array_column($materiels, 'type_equipement')));
sort($types_disponibles);

// Appliquer les filtres
$materiels_filtres = array_filter($materiels, function($mat) use ($filtre_type, $filtre_societe, $filtre_statut) {
    if ($filtre_type    && ($mat['type_equipement'] ?? '') !== $filtre_type)    return false;
    if ($filtre_societe && ($mat['societe'] ?? '')         !== $filtre_societe) return false;
    if ($filtre_statut  && ($mat['statut'] ?? '')          !== $filtre_statut)  return false;
    return true;
});
?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Gestion du Matériel</h1>
            <p class="page-subtitle">Inventaire complet de tous vos équipements</p>
        </div>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            ✅ Équipement supprimé avec succès !
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="stat-label">Total Matériel</div>
                <div class="stat-value"><?php echo count($materiels); ?></div>
                <div class="stat-info"><?php echo count($materiels_filtres); ?> affiché(s)</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="content-card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h2 class="card-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filtres
                </h2>
                <?php if ($filtre_type || $filtre_societe || $filtre_statut): ?>
                <a href="materiel.php" class="btn btn-secondary btn-small">✕ Réinitialiser</a>
                <?php endif; ?>
            </div>
            <form method="GET" style="display:flex; flex-wrap:wrap; gap:1rem; padding:1.25rem 1.5rem; align-items:flex-end;">
                <div style="flex:1; min-width:180px;">
                    <label style="font-size:12px; color:var(--text-medium); text-transform:uppercase; letter-spacing:.5px; font-weight:600; display:block; margin-bottom:.4rem;">Type d'équipement</label>
                    <select name="type" style="width:100%;">
                        <option value="">Tous les types</option>
                        <option value="Ordinateur Portable" <?php echo $filtre_type === 'Ordinateur Portable' ? 'selected' : ''; ?>>Ordinateur Portable</option>
                        <option value="Ordinateur Fixe"     <?php echo $filtre_type === 'Ordinateur Fixe'     ? 'selected' : ''; ?>>Ordinateur Fixe</option>
                        <option value="Imprimante"          <?php echo $filtre_type === 'Imprimante'          ? 'selected' : ''; ?>>Imprimante</option>
                        <option value="Écran"               <?php echo $filtre_type === 'Écran'               ? 'selected' : ''; ?>>Écran</option>
                        <option value="Autre"               <?php echo $filtre_type === 'Autre'               ? 'selected' : ''; ?>>Autre</option>
                        <?php foreach ($types_disponibles as $t): 
                            if (!in_array($t, ['Ordinateur Portable','Ordinateur Fixe','Imprimante','Écran','Autre'])): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo $filtre_type === $t ? 'selected' : ''; ?>><?php echo htmlspecialchars($t); ?></option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>

                <div style="flex:1; min-width:160px;">
                    <label style="font-size:12px; color:var(--text-medium); text-transform:uppercase; letter-spacing:.5px; font-weight:600; display:block; margin-bottom:.4rem;">Société</label>
                    <select name="societe" style="width:100%;">
                        <option value="">Toutes les sociétés</option>
                        <option value="AHI"           <?php echo $filtre_societe === 'AHI'           ? 'selected' : ''; ?>>AHI</option>
                        <option value="Isra Voyages"  <?php echo $filtre_societe === 'Isra Voyages'  ? 'selected' : ''; ?>>Isra Voyages</option>
                        <option value="Cenaze şirketi"<?php echo $filtre_societe === 'Cenaze şirketi'? 'selected' : ''; ?>>Cenaze şirketi</option>
                        <option value="Ditib France"  <?php echo $filtre_societe === 'Ditib France'  ? 'selected' : ''; ?>>Ditib France</option>
                    </select>
                </div>

                <div style="flex:1; min-width:140px;">
                    <label style="font-size:12px; color:var(--text-medium); text-transform:uppercase; letter-spacing:.5px; font-weight:600; display:block; margin-bottom:.4rem;">Statut</label>
                    <select name="statut" style="width:100%;">
                        <option value="">Tous les statuts</option>
                        <option value="actif"        <?php echo $filtre_statut === 'actif'        ? 'selected' : ''; ?>>Actif</option>
                        <option value="maintenance"  <?php echo $filtre_statut === 'maintenance'  ? 'selected' : ''; ?>>En maintenance</option>
                        <option value="stockage"     <?php echo $filtre_statut === 'stockage'     ? 'selected' : ''; ?>>En stockage</option>
                        <option value="hors-service" <?php echo $filtre_statut === 'hors-service' ? 'selected' : ''; ?>>Hors service</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </form>

            <?php if ($filtre_type || $filtre_societe || $filtre_statut): ?>
            <div style="padding:0 1.5rem 1rem; display:flex; gap:.5rem; flex-wrap:wrap;">
                <?php if ($filtre_type): ?>
                <span style="background:#ede9fe;color:#5b21b6;padding:.3rem .75rem;border-radius:4px;font-size:12px;font-weight:500;">
                    Type : <?php echo htmlspecialchars($filtre_type); ?>
                </span>
                <?php endif; ?>
                <?php if ($filtre_societe): ?>
                <span style="background:#dbeafe;color:#1e40af;padding:.3rem .75rem;border-radius:4px;font-size:12px;font-weight:500;">
                    Société : <?php echo htmlspecialchars($filtre_societe); ?>
                </span>
                <?php endif; ?>
                <?php if ($filtre_statut): ?>
                <span style="background:#d1fae5;color:#065f46;padding:.3rem .75rem;border-radius:4px;font-size:12px;font-weight:500;">
                    Statut : <?php echo htmlspecialchars($filtre_statut); ?>
                </span>
                <?php endif; ?>
                <span style="color:var(--text-light);font-size:12px;padding:.3rem 0;">
                    — <?php echo count($materiels_filtres); ?> résultat(s)
                </span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tableau -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Liste du Matériel</h2>
                <a href="ajout-materiel.php" class="btn btn-primary btn-small">Ajouter</a>
            </div>
            
            <?php if (count($materiels_filtres) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Société</th>
                            <th>Type</th>
                            <th>Marque/Modèle</th>
                            <th>Emplacement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiels_filtres as $mat): 
                            $statut = $mat['statut'] ?? 'actif';
                            $badgeClass = 'badge-success';
                            if ($statut === 'maintenance') $badgeClass = 'badge-warning';
                            elseif ($statut === 'hors-service') $badgeClass = 'badge-danger';
                            elseif ($statut === 'stockage') $badgeClass = 'badge-info';
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($mat['id_equipement'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($mat['societe'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($mat['type_equipement'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(($mat['marque'] ?? '') . ' ' . ($mat['modele'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($mat['emplacement'] ?? '-'); ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($statut); ?></span></td>
                            <td>
                                <a href="detail-materiel.php?id=<?php echo $mat['id']; ?>" class="action-btn action-view">Voir</a>
                                <a href="modifier-materiel.php?id=<?php echo $mat['id']; ?>" class="action-btn action-edit">Modifier</a>
                                <a href="supprimer-materiel.php?id=<?php echo $mat['id']; ?>" class="action-btn action-delete">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif (count($materiels) > 0): ?>
            <p style="text-align: center; padding: 3rem; color: var(--text-medium);">
                Aucun équipement ne correspond aux filtres sélectionnés.<br>
                <a href="materiel.php" style="color: var(--primary-color);">Réinitialiser les filtres</a>
            </p>
            <?php else: ?>
            <p style="text-align: center; padding: 3rem; color: var(--text-medium);">
                Aucun matériel. <a href="ajout-materiel.php" style="color: var(--primary-color);">Ajoutez-en un !</a>
            </p>
            <?php endif; ?>
        </div>
    </div>

<?php require_once 'footer.php'; ?>