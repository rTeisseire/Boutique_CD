<?php
require_once '../includes/functions.php';

// Rediriger si déjà connecté
if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $erreur = 'Identifiants incorrects';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - CD Shop</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🔐 Administration</h1>
            <h2>CD Shop</h2>

            <?php if ($erreur): ?>
                <div class="alert error"><?= e($erreur) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text"
                           id="username"
                           name="username"
                           required
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password"
                           id="password"
                           name="password"
                           required>
                </div>

                <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">
                    Se connecter
                </button>
            </form>

            <div class="login-info">
                <p><a href="../index.php">← Retour au site</a></p>
                <p><small>Par défaut: admin / admin123</small></p>
            </div>
        </div>
    </div>
</body>
</html>