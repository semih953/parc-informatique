<?php
$page_title = 'Changer le mot de passe - Parc Informatique DITIB France';
$current_page = 'profil';
require_once 'dataManager.php';

$users_file = __DIR__ . '/users.json';
$message = '';
$error = '';

if (!file_exists($users_file)) {
    die('Fichier users.json introuvable.');
}

$user_data = json_decode(file_get_contents($users_file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mdp_actuel    = $_POST['mdp_actuel']    ?? '';
    $mdp_nouveau   = $_POST['mdp_nouveau']   ?? '';
    $mdp_confirmer = $_POST['mdp_confirmer'] ?? '';

    if (!password_verify($mdp_actuel, $user_data['password_hash'])) {
        $error = 'Le mot de passe actuel est incorrect.';
    } elseif (strlen($mdp_nouveau) < 8) {
        $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
    } elseif ($mdp_nouveau !== $mdp_confirmer) {
        $error = 'Les deux nouveaux mots de passe ne correspondent pas.';
    } elseif (password_verify($mdp_nouveau, $user_data['password_hash'])) {
        $error = 'Le nouveau mot de passe doit être différent de l\'ancien.';
    } else {
        // Tout est OK : mettre à jour le hash
        $user_data['password_hash'] = password_hash($mdp_nouveau, PASSWORD_BCRYPT);
        file_put_contents($users_file, json_encode($user_data, JSON_PRETTY_PRINT));
        $message = 'Mot de passe modifié avec succès !';
    }
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Changer le mot de passe</h1>
            <p class="page-subtitle">Modifier vos identifiants de connexion</p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Retour</a>
    </div>

    <div style="max-width: 520px;">

        <?php if ($message): ?>
        <div style="background:#d1fae5;color:#065f46;padding:1rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;font-weight:500;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:1rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;font-weight:500;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
            </svg>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18" style="display:inline;vertical-align:middle;margin-right:6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Modifier le mot de passe
                </h2>
            </div>

            <form method="POST" style="padding:1.5rem;" autocomplete="off">

                <!-- Identifiant (lecture seule) -->
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Identifiant</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled
                           style="background:#f8fafc;color:#64748b;cursor:not-allowed;">
                </div>

                <!-- Mot de passe actuel -->
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Mot de passe actuel <span style="color:#ef4444;">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="mdp_actuel" id="mdp_actuel" required
                               style="padding-right:3rem;" autocomplete="current-password">
                        <button type="button" onclick="togglePwd('mdp_actuel')"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;">
                            <?php echo eye_icon('mdp_actuel'); ?>
                        </button>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--border-light);margin:1.5rem 0;">

                <!-- Nouveau mot de passe -->
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label>Nouveau mot de passe <span style="color:#ef4444;">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="mdp_nouveau" id="mdp_nouveau" required
                               minlength="8" style="padding-right:3rem;" autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePwd('mdp_nouveau')"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;">
                            <?php echo eye_icon('mdp_nouveau'); ?>
                        </button>
                    </div>
                    <!-- Indicateur de force -->
                    <div style="margin-top:.5rem;">
                        <div style="height:4px;background:#e2e8f0;border-radius:2px;overflow:hidden;">
                            <div id="strength-bar" style="height:100%;width:0;transition:all .3s;border-radius:2px;"></div>
                        </div>
                        <p id="strength-text" style="font-size:11px;color:#94a3b8;margin-top:.35rem;"></p>
                    </div>
                    <p style="font-size:11px;color:#94a3b8;margin-top:.25rem;">Minimum 8 caractères</p>
                </div>

                <!-- Confirmer nouveau mot de passe -->
                <div class="form-group" style="margin-bottom:1.75rem;">
                    <label>Confirmer le nouveau mot de passe <span style="color:#ef4444;">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="mdp_confirmer" id="mdp_confirmer" required
                               style="padding-right:3rem;" autocomplete="new-password"
                               oninput="checkMatch()">
                        <button type="button" onclick="togglePwd('mdp_confirmer')"
                                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;">
                            <?php echo eye_icon('mdp_confirmer'); ?>
                        </button>
                    </div>
                    <p id="match-text" style="font-size:11px;margin-top:.35rem;"></p>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:.75rem;font-size:14px;justify-content:center;">
                    Enregistrer le nouveau mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

<?php
function eye_icon($id) {
    return '<svg id="eye-'.$id.'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
    </svg>';
}
?>

<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    const svg = document.getElementById('eye-' + id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    svg.innerHTML = isHidden
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
}

function checkStrength(val) {
    const bar  = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%',   color: '#e2e8f0', label: '' },
        { pct: '25%',  color: '#ef4444', label: 'Très faible' },
        { pct: '50%',  color: '#f97316', label: 'Faible' },
        { pct: '75%',  color: '#eab308', label: 'Moyen' },
        { pct: '90%',  color: '#22c55e', label: 'Fort' },
        { pct: '100%', color: '#16a34a', label: 'Très fort' },
    ];
    const lvl = levels[Math.min(score, 5)];
    bar.style.width  = lvl.pct;
    bar.style.background = lvl.color;
    text.textContent = lvl.label;
    text.style.color = lvl.color;
}

function checkMatch() {
    const n = document.getElementById('mdp_nouveau').value;
    const c = document.getElementById('mdp_confirmer').value;
    const el = document.getElementById('match-text');
    if (c === '') { el.textContent = ''; return; }
    if (n === c) {
        el.textContent = '✓ Les mots de passe correspondent';
        el.style.color = '#16a34a';
    } else {
        el.textContent = '✗ Les mots de passe ne correspondent pas';
        el.style.color = '#ef4444';
    }
}
</script>

<?php require_once 'footer.php'; ?>
