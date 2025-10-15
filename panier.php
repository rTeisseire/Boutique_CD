<?php
require_once 'include.php';

// Gestion des actions
if (isset($_POST['retirer'])) {
    $id_cd = intval($_POST['id_cd']);
    retirerDuPanier($id_cd);
    header('Location: panier.php');
    exit;
}

if (isset($_POST['vider'])) {
    viderPanier();
    header('Location: panier.php');
    exit;
}

$articles = getPanierDetails();
$total = calculerTotal();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - CD Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="container">
        <h2>Mon Panier</h2>

        <?php if (empty($articles)): ?>
            <div class="alert info">
                Votre panier est vide.
                <a href="index.php">Retourner au catalogue</a>
            </div>
        <?php else: ?>
            <table class="panier-table">
                <thead>
                    <tr>
                        <th>Pochette</th>
                        <th>Titre</th>
                        <th>Artiste</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <img src="images/pochettes/<?= e($article['image']) ?>"
                                     alt="<?= e($article['titre']) ?>"
                                     class="panier-thumb"
                                     onerror="this.src='images/pochettes/default.png'">
                            </td>
                            <td><?= e($article['titre']) ?></td>
                            <td><?= e($article['auteur']) ?></td>
                            <td><?= formatPrix($article['prix']) ?></td>
                            <td><?= $article['quantite'] ?></td>
                            <td><?= formatPrix($article['prix'] * $article['quantite']) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="id_cd" value="<?= $article['id'] ?>">
                                    <button type="submit" name="retirer" class="btn btn-danger btn-small">
                                        Retirer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;"><strong>Total :</strong></td>
                        <td colspan="2"><strong><?= formatPrix($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="panier-actions">
                <form method="post" style="display:inline;">
                    <button type="submit" name="vider" class="btn btn-secondary"
                            onclick="return confirm('Êtes-vous sûr de vouloir vider le panier ?')">
                        Vider le panier
                    </button>
                </form>
                <a href="index.php" class="btn btn-secondary">Continuer mes achats</a>
                <a href="paiement.php" class="btn btn-primary btn-large">Procéder au paiement</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>