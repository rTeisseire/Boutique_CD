<?php
require_once 'include.php';

// Gestion de l'ajout au panier
if (isset($_POST['ajouter_panier'])) {
    $id_cd = intval($_POST['id_cd']);
    ajouterAuPanier($id_cd);
    $message = "CD ajouté au panier !";
}

$cds = getAllCDs();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CD Shop - Boutique en ligne</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="container">
        <?php if (isset($message)): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <h2>Catalogue de CD</h2>

        <div class="cd-grid">
            <?php foreach ($cds as $cd): ?>
                <div class="cd-card">
                    <a href="detail.php?id=<?= $cd['id'] ?>">
                        <img src="images/pochettes/<?= e($cd['image']) ?>"
                            alt="<?= e($cd['titre']) ?>"
                            onerror="this.src='images/pochettes/default.jpg'">
                    </a>
                    <div class="cd-info">
                        <h3><?= e($cd['titre']) ?></h3>
                        <p class="auteur"><?= e($cd['auteur']) ?></p>
                        <p class="genre"><?= e($cd['genre']) ?></p>
                        <p class="prix"><?= formatPrix($cd['prix']) ?></p>
                        <div class="actions">
                            <a href="detail.php?id=<?= $cd['id'] ?>" class="btn btn-secondary">Détails</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_cd" value="<?= $cd['id'] ?>">
                                <button type="submit" name="ajouter_panier" class="btn btn-primary">
                                    Ajouter au panier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>