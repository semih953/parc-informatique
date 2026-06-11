<?php
$page_title = 'Détail Compte - Parc Informatique DITIB France';
$current_page = 'comptes';
require_once 'dataManager.php';
require_once 'cryptoHelper.php';

$id = $_GET['id'] ?? null;
$compte = null;

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

$identifiant = decryptData($compte['identifiant'] ?? '');
$mot_de_passe = decryptData($compte['mot_de_passe'] ?? '');
require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">🔐 Détail du Compte</h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($compte['nom']); ?></p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Informations du Compte</h2>
            <div style="display: flex; gap: 1rem;">
                <a href="modifier-compte.php?id=<?php echo $compte['id']; ?>" class="btn btn-primary btn-small">Modifier</a>
                <a href="comptes.php" class="btn btn-secondary btn-small">Retour</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Nom du Compte</div>
                <div class="detail-value" style="font-size: 16px; font-weight: 600;">
                    <?php echo htmlspecialchars($compte['nom'] ?? '-'); ?>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Service / Type</div>
                <div class="detail-value">
                    <?php if (!empty($compte['service'])): ?>
                    <span class="badge" style="background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd;">
                        <?php echo htmlspecialchars($compte['service']); ?>
                    </span>
                    <?php else: echo '-'; endif; ?>
                </div>
            </div>

            <?php if (!empty($compte['url'])): ?>
            <div class="detail-item">
                <div class="detail-label">URL</div>
                <div class="detail-value">
                    <a href="<?php echo htmlspecialchars($compte['url']); ?>" target="_blank"
                       style="color: var(--accent-color);">
                        🔗 <?php echo htmlspecialchars($compte['url']); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Zone sécurisée -->
        <div style="margin: 1.5rem; background: #1e293b; border-radius: 10px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <span style="font-size: 18px;">🔒</span>
                <span style="color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                    Zone Sécurisée
                </span>
            </div>

            <!-- Identifiant -->
            <div style="margin-bottom: 1.25rem;">
                <div style="color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                    Identifiant / Email
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div id="id_masked" style="font-family: monospace; font-size: 15px; color: #64748b; letter-spacing: 2px; flex:1;">
                        ●●●●●●●●●●●●
                    </div>
                    <div id="id_value" style="font-family: monospace; font-size: 15px; color: #e2e8f0; flex:1; display:none;">
                        <?php echo htmlspecialchars($identifiant ?: '-'); ?>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="toggleField('id')"
                                style="background:#334155;border:none;color:#94a3b8;padding:0.4rem 0.75rem;border-radius:6px;cursor:pointer;font-size:13px;" id="id_btn">
                            Afficher
                        </button>
                        <button onclick="copyToClipboard('<?php echo addslashes(htmlspecialchars($identifiant)); ?>', this)"
                                style="background:#334155;border:none;color:#94a3b8;padding:0.4rem 0.75rem;border-radius:6px;cursor:pointer;font-size:13px;">
                            Copier
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mot de Passe -->
            <div>
                <div style="color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                    Mot de Passe
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div id="mdp_masked" style="font-family: monospace; font-size: 15px; color: #64748b; letter-spacing: 2px; flex:1;">
                        ●●●●●●●●●●●●
                    </div>
                    <div id="mdp_value" style="font-family: monospace; font-size: 15px; color: #e2e8f0; flex:1; display:none;">
                        <?php echo htmlspecialchars($mot_de_passe ?: '-'); ?>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="toggleField('mdp')"
                                style="background:#334155;border:none;color:#94a3b8;padding:0.4rem 0.75rem;border-radius:6px;cursor:pointer;font-size:13px;" id="mdp_btn">
                            Afficher
                        </button>
                        <button onclick="copyToClipboard('<?php echo addslashes(htmlspecialchars($mot_de_passe)); ?>', this)"
                                style="background:#334155;border:none;color:#94a3b8;padding:0.4rem 0.75rem;border-radius:6px;cursor:pointer;font-size:13px;">
                            Copier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($compte['notes'])): ?>
        <div style="margin: 1.5rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px;">
            <h3 style="margin-bottom: 0.5rem; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Notes</h3>
            <p style="color: #64748b; font-size: 14px;"><?php echo nl2br(htmlspecialchars($compte['notes'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($compte['date_creation'])): ?>
        <div style="margin: 1.5rem; padding: 1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.8rem; color: #94a3b8;">
            Ajouté le <?php echo date('d/m/Y à H:i', strtotime($compte['date_creation'])); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleField(field) {
    const masked = document.getElementById(field + '_masked');
    const value  = document.getElementById(field + '_value');
    const btn    = document.getElementById(field + '_btn');
    if (masked.style.display === 'none') {
        masked.style.display = 'block';
        value.style.display  = 'none';
        btn.textContent = 'Afficher';
    } else {
        masked.style.display = 'none';
        value.style.display  = 'block';
        btn.textContent = 'Masquer';
    }
}

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✓ Copié';
        btn.style.color = '#10b981';
        setTimeout(() => { btn.textContent = orig; btn.style.color = '#94a3b8'; }, 2000);
    });
}
</script>

<?php require_once 'footer.php'; ?>