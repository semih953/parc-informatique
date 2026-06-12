<?php
$page_title = 'Changer le mot de passe - Parc Informatique DITIB France';
$current_page = 'changer-mdp';
require_once __DIR__ . '/auth.php';

$users_file = __DIR__ . '/users.json';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // (la vérification CSRF est déjà faite automatiquement par auth.php)
    $ancien = $_POST['ancien_mdp'] ?? '';
    $nouveau = $_POST['nouveau_mdp'] ?? '';
    $confirmation = $_POST['confirmation_mdp'] ?? '';

    $users = json_decode(file_get_contents($users_file), true) ?? [];
    $username = $_SESSION['username'] ?? '';

    $found = false;
    foreach ($users as $key => $user) {
        if ($user['username'] === $username) {
            $found = true;

            if (!password_verify($ancien, $user['password_hash'] ?? '')) {
                $error = 'L\'ancien mot de passe est incorrect.';
            } elseif (strlen($nouveau) < 10) {
                $error = 'Le nouveau mot de passe doit contenir au moins 10 caractères.';
            } elseif (!preg_match('/[A-Za-z]/', $nouveau) || !preg_match('/[0-9]/', $nouveau)) {
                $error = 'Le nouveau mot de passe doit contenir au moins une lettre et un chiffre.';
            } elseif ($nouveau === $ancien) {
                $error = 'Le nouveau mot de passe doit être différent de l\'ancien.';
            } elseif ($nouveau !== $confirmation) {
                $error = 'Les deux mots de passe ne correspondent pas.';
            } else {
                $users[$key]['password_hash'] = password_hash($nouveau, PASSWORD_BCRYPT);
                file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT), LOCK_EX);

                // Régénérer la session après changement de mot de passe
                session_regenerate_id(true);

                $message = '✅ Mot de passe modifié avec succès !';
            }
            break;
        }
    }

    if (!$found) {
        $error = 'Utilisateur introuvable.';
    }
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Changer le mot de passe</h1>
        <p class="page-subtitle">Compte : <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
    </div>

    <?php if ($message): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <div class="content-card">
        <form method="POST" style="padding: 1.5rem; max-width: 480px;">
                <?php echo csrf_field(); ?>
            <?php echo csrf_field(); ?>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label>Ancien mot de passe *</label>
                <input type="password" name="ancien_mdp" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label>Nouveau mot de passe * (10 caractères min., lettres + chiffres)</label>
                <input type="password" name="nouveau_mdp" required minlength="10">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label>Confirmer le nouveau mot de passe *</label>
                <input type="password" name="confirmation_mdp" required minlength="10">
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Modifier le mot de passe</button>
                <a href="index.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>