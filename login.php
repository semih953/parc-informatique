<?php
require_once __DIR__ . '/security.php';

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$users_file = __DIR__ . '/users.json';
$attempts_file = __DIR__ . '/data/login_attempts.json';
$error = '';
$info = '';

if (isset($_GET['expired'])) {
    $info = 'Votre session a expiré, veuillez vous reconnecter.';
}

// ===== ANTI-BRUTE-FORCE : 5 essais max par 15 minutes =====
const MAX_ATTEMPTS = 5;
const LOCKOUT_SECONDS = 900; // 15 minutes

function load_attempts($filepath) {
    if (!file_exists($filepath)) return [];
    $data = json_decode(file_get_contents($filepath), true) ?? [];
    // Nettoyer les entrées expirées
    $now = time();
    foreach ($data as $key => $entry) {
        if (($now - ($entry['first'] ?? 0)) > LOCKOUT_SECONDS) {
            unset($data[$key]);
        }
    }
    return $data;
}

function save_attempts($filepath, $data) {
    $dir = dirname($filepath);
    if (!is_dir($dir)) mkdir($dir, 0750, true);
    file_put_contents($filepath, json_encode($data), LOCK_EX);
}

function attempt_key() {
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

// ===== CHARGEMENT DES UTILISATEURS + MIGRATION BCRYPT =====
function load_users($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }

    $users = json_decode(file_get_contents($filepath), true) ?? [];
    $modified = false;

    foreach ($users as $key => $user) {
        if (isset($user['password_plain_init'])) {
            $users[$key]['password_hash'] = password_hash($user['password_plain_init'], PASSWORD_BCRYPT);
            unset($users[$key]['password_plain_init']);
            $modified = true;
        }
    }

    if ($modified) {
        file_put_contents($filepath, json_encode($users, JSON_PRETTY_PRINT), LOCK_EX);
    }

    return $users;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    csrf_verify();

    $attempts = load_attempts($attempts_file);
    $key = attempt_key();
    $entry = $attempts[$key] ?? ['count' => 0, 'first' => time()];

    if ($entry['count'] >= MAX_ATTEMPTS) {
        $remaining = ceil((LOCKOUT_SECONDS - (time() - $entry['first'])) / 60);
        $error = "Trop de tentatives échouées. Réessayez dans environ {$remaining} minute(s).";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $users = load_users($users_file);
        $authenticated = false;

        foreach ($users as $user) {
            if (hash_equals($user['username'], $username) && password_verify($password, $user['password_hash'] ?? '')) {
                $authenticated = true;
                break;
            }
        }

        if ($authenticated) {
            // Réinitialiser le compteur d'essais
            unset($attempts[$key]);
            save_attempts($attempts_file, $attempts);

            // Nouvelle session propre (anti-fixation de session)
            session_regenerate_id(true);

            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['last_activity'] = time();
            $_SESSION['regenerated_at'] = time();
            $_SESSION['agent_hash'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');

            header('Location: index.php');
            exit;
        } else {
            // Enregistrer la tentative échouée
            $entry['count']++;
            if ($entry['count'] === 1) $entry['first'] = time();
            $attempts[$key] = $entry;
            save_attempts($attempts_file, $attempts);

            // Ralentir les attaques automatisées
            usleep(random_int(300000, 800000));

            $error = 'Identifiants incorrects';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Parc Informatique DITIB</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-logo {
            width: 80px;
            height: auto;
            margin: 0 auto 1rem;
            display: block;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .login-form {
            margin-top: 2rem;
        }

        .login-form .form-group {
            margin-bottom: 1.5rem;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .login-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .info-message {
            background: #dbeafe;
            color: #1e40af;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="logoditib.png" alt="Logo DITIB" class="login-logo">
            <h1 class="login-title">Connexion</h1>
            <p class="login-subtitle">Parc Informatique DITIB France</p>
        </div>

        <?php if ($info): ?>
        <div class="info-message">
            <?php echo htmlspecialchars($info); ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
                <?php echo csrf_field(); ?>
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" placeholder="Entrez votre nom d'utilisateur" required autofocus>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="Entrez votre mot de passe" required>
            </div>

            <button type="submit" class="btn-login">Se connecter</button>
        </form>
    </div>
</body>
</html>