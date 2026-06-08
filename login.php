<?php
session_start();

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$users_file = __DIR__ . '/users.json';
$error = '';

// Initialisation : si le mot de passe n'est pas encore hashé, le hasher maintenant
function init_users_file($filepath) {
    if (!file_exists($filepath)) {
        // Créer un fichier par défaut si inexistant
        $default = [
            'username' => 'oguzhan.yildiz@ditibfrance.fr',
            'password_hash' => password_hash('Ditib9325', PASSWORD_BCRYPT)
        ];
        file_put_contents($filepath, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }

    $data = json_decode(file_get_contents($filepath), true);

    // Si on trouve un mot de passe en clair à hasher (migration initiale)
    if (isset($data['password_plain_init'])) {
        $data['password_hash'] = password_hash($data['password_plain_init'], PASSWORD_BCRYPT);
        unset($data['password_plain_init']); // Supprimer le mot de passe en clair
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
    }

    return $data;
}

$user_data = init_users_file($users_file);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $username === $user_data['username'] &&
        password_verify($password, $user_data['password_hash'])
    ) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects';
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

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            padding: 0;
            display: flex;
            align-items: center;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }
        
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: 'Roboto', sans-serif;
            background-color: #fff;
            color: #1f1f1f;
        }
        
        .login-form input:focus {
            outline: none;
            border-color: #714B67;
            box-shadow: 0 0 0 3px rgba(113, 75, 103, 0.1);
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
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #714B67;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Roboto', sans-serif;
        }
        
        .btn-login:hover {
            background: #5a3c52;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(113, 75, 103, 0.3);
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
        
        <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label>Identifiant</label>
                <input type="text" name="username" placeholder="Votre identifiant" required autofocus
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" title="Afficher/Masquer">
                        <svg id="eye-icon-password" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.querySelector('svg').innerHTML = isHidden
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    </script>
</body>
</html>