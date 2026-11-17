<?php
$page_title = 'Changer le mot de passe - Parc Informatique DITIB France';
$current_page = 'changer-mdp';
require_once __DIR__ . '/auth.php';

$message = '';
$error = '';
$force = !empty($__current_user['must_change']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // (la vérification CSRF est déjà faite automatiquement par auth.php)
    $ancien = $_POST['ancien_mdp'] ?? '';
    $nouveau = $_POST['nouveau_mdp'] ?? '';
    $confirmation = $_POST['confirmation_mdp'] ?? '';

    $users = users_load();
    $key = users_find($users, $_SESSION['username'] ?? '');

    if ($key < 0) {
        $error = 'Utilisateur introuvable.';
    } elseif (!password_verify($ancien, $users[$key]['password_hash'] ?? '')) {
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
        unset($users[$key]['must_change']);
        users_save($users);

        // Régénérer la session après changement de mot de passe
        session_regenerate_id(true);

        if ($force) {
            header('Location: index.php');
            exit;
        }
        $message = '✅ Mot de passe modifié avec succès !';
    }
}

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Changer le mot de passe</h1>
        <p class="page-subtitle">Compte : <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></p>
    </div>

    <?php if ($force): ?>
    <div style="background: #dbeafe; color: #1e40af; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
        🔑 Vous vous connectez avec un mot de passe provisoire. Choisissez votre propre mot de passe pour continuer.
    </div>
    <?php endif; ?>

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
                <?php if ($force): ?>
                <a href="logout.php" class="btn btn-secondary">Se déconnecter</a>
                <?php else: ?>
                <a href="index.php" class="btn btn-secondary">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>