<?php
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);
$cd = getCDById($id);

if (!$cd) {
    header('Location: index.php');
    exit;
}

// Gestion de l'ajout au panier
if (isset($_POST['ajouter_panier'])) {
    ajouterAuPanier($cd['id']);
    $message = "CD ajouté au panier !";
}

$nb_panier = array_sum($_SESSION['panier']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($cd['titre']) ?> - CD Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🎵 CD Shop</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="panier.php">🛒 Panier (<?= $nb_panier ?>)</a>
                <a href="admin/login.php">Admin</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($message)): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="cd-detail">
            <div class="cd-image-large">
                <img src="images/pochettes/<?= e($cd['image']) ?>"
                     alt="<?= e($cd['titre']) ?>"
                     onerror="this.src='images/pochettes/default.jpg'">
            </div>

            <div class="cd-details-info">
                <h2><?= e($cd['titre']) ?></h2>

                <div class="detail-row">
                    <strong>Artiste :</strong>
                    <span><?= e($cd['auteur']) ?></span>
                </div>

                <div class="detail-row">
                    <strong>Genre :</strong>
                    <span><?= e($cd['genre']) ?></span>
                </div>

                <div class="detail-row">
                    <strong>Prix :</strong>
                    <span class="prix-large"><?= formatPrix($cd['prix']) ?></span>
                </div>

                <div class="actions-detail">
                    <form method="post">
                        <button type="submit" name="ajouter_panier" class="btn btn-primary btn-large">
                            🛒 Ajouter au panier
                        </button>
                    </form>
                    <a href="index.php" class="btn btn-secondary">← Retour au catalogue</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 CD Shop - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>