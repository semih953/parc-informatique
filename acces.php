<?php
$page_title = 'Accès - Parc Informatique DITIB France';
$current_page = 'acces';
require_once __DIR__ . '/auth.php';
require_admin();

$message = '';
$error = '';
$temp_password = '';   // affiché une seule fois après création / réinitialisation
$temp_for = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // (CSRF déjà vérifié par auth.php)
    $action = $_POST['action'] ?? '';
    $users = users_load();
    $me = users_normalize($_SESSION['username']);

    if ($action === 'create') {
        $email = users_normalize($_POST['username'] ?? '');
        $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (users_find($users, $email) >= 0) {
            $error = 'Un accès existe déjà pour cette adresse.';
        } else {
            $temp_password = generate_temp_password();
            $temp_for = $email;
            $users[] = [
                'username'      => $email,
                'password_hash' => password_hash($temp_password, PASSWORD_BCRYPT),
                'role'          => $role,
                'must_change'   => true,
                'created_at'    => date('Y-m-d H:i'),
            ];
            users_save($users);
            $message = "✅ Accès créé pour {$email}.";
        }
    } else {
        $target = users_normalize($_POST['username'] ?? '');
        $i = users_find($users, $target);

        if ($i < 0) {
            $error = 'Compte introuvable.';
        } elseif ($action === 'reset') {
            $temp_password = generate_temp_password();
            $temp_for = $target;
            $users[$i]['password_hash'] = password_hash($temp_password, PASSWORD_BCRYPT);
            $users[$i]['must_change'] = true;
            users_save($users);
            $message = "✅ Mot de passe réinitialisé pour {$target}.";
        } elseif ($action === 'toggle_role') {
            $isAdmin = ($users[$i]['role'] ?? '') === 'admin';
            if ($isAdmin && users_count_admins($users) <= 1) {
                $error = 'Impossible : il doit rester au moins un administrateur.';
            } elseif ($target === $me) {
                $error = 'Vous ne pouvez pas modifier votre propre rôle.';
            } else {
                $users[$i]['role'] = $isAdmin ? 'user' : 'admin';
                users_save($users);
                $message = "✅ Rôle de {$target} modifié.";
            }
        } elseif ($action === 'delete') {
            if ($target === $me) {
                $error = 'Vous ne pouvez pas supprimer votre propre accès.';
            } elseif (($users[$i]['role'] ?? '') === 'admin' && users_count_admins($users) <= 1) {
                $error = 'Impossible : il doit rester au moins un administrateur.';
            } else {
                array_splice($users, $i, 1);
                users_save($users);
                $message = "✅ Accès de {$target} supprimé.";
            }
        } else {
            $error = 'Action inconnue.';
        }
    }
}

$users = users_load();
usort($users, fn($a, $b) => strcmp($a['username'], $b['username']));
$me = users_normalize($_SESSION['username']);

require_once 'header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Gestion des accès</h1>
        <p class="page-subtitle">Comptes pouvant se connecter au parc informatique</p>
    </div>

    <?php if ($message): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <?php if ($temp_password): ?>
    <div style="background: #fef3c7; color: #92400e; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fcd34d;">
        <strong>Mot de passe provisoire pour <?php echo htmlspecialchars($temp_for); ?> :</strong>
        <code style="font-size: 1.1rem; background: #fff; padding: 0.2rem 0.6rem; border-radius: 4px; margin-left: 0.5rem; user-select: all;"><?php echo htmlspecialchars($temp_password); ?></code>
        <p style="margin-top: 0.5rem; font-size: 0.875rem;">
            Notez-le maintenant, il ne sera plus affiché. La personne devra le changer à sa première connexion.
        </p>
    </div>
    <?php endif; ?>

    <div class="content-card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h2 class="card-title">Créer un accès</h2>
        </div>
        <form method="POST" style="padding: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="create">
            <div class="form-group" style="flex: 2; min-width: 240px; margin: 0;">
                <label>Email *</label>
                <input type="email" name="username" placeholder="prenom.nom@ditibfrance.fr" required autocomplete="off">
            </div>
            <div class="form-group" style="flex: 1; min-width: 160px; margin: 0;">
                <label>Rôle</label>
                <select name="role">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer l'accès</button>
        </form>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2 class="card-title">Accès existants (<?php echo count($users); ?>)</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <?php $isMe = users_normalize($u['username']) === $me; ?>
                    <?php $isAdm = ($u['role'] ?? '') === 'admin'; ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($u['username']); ?></strong>
                            <?php if ($isMe): ?><span style="color: var(--text-light);"> (vous)</span><?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $isAdm ? 'badge-info' : ''; ?>">
                                <?php echo $isAdm ? 'Administrateur' : 'Utilisateur'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($u['must_change'])): ?>
                            <span style="color: #b45309;">Mot de passe provisoire</span>
                            <?php else: ?>
                            <span style="color: #047857;">Actif</span>
                            <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap;">
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Générer un nouveau mot de passe provisoire pour <?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?> ?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="reset">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($u['username']); ?>">
                                <button type="submit" class="action-btn action-edit">Réinitialiser MDP</button>
                            </form>
                            <?php if (!$isMe): ?>
                            <form method="POST" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="toggle_role">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($u['username']); ?>">
                                <button type="submit" class="action-btn action-view"><?php echo $isAdm ? 'Retirer admin' : 'Passer admin'; ?></button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer l\'accès de <?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?> ?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($u['username']); ?>">
                                <button type="submit" class="action-btn action-delete">Supprimer</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>