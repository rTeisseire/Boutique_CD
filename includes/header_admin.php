<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CD Shop</title>
    <link rel="stylesheet" href="../style.css">
    <?php if (file_exists(__DIR__ . '/../style_admin.css')): ?>
        <link rel="stylesheet" href="/admin/style_admin.css">
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="container">
            <h1>CD Shop - Administration</h1>
            <nav>
                <span>👤 <?= e($_SESSION['admin_username']) ?></span>
                <a href="../index.php">Voir le site</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </div>
    </header>
</body>
