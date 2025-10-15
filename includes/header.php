<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nb_panier = array_sum($_SESSION['panier']);
?>
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
            <h1>CD Shop</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="panier.php">Panier (<?= $nb_panier ?>)</a>
                <a href="admin/login.php">Admin</a>
            </nav>
        </div>
    </header>
    </header>

    <main class="site-content">
