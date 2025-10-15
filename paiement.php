<?php
require_once 'include.php';

// Rediriger si le panier est vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

$articles = getPanierDetails();
$total = calculerTotal();
$erreur = '';
$succes = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_carte = $_POST['numero_carte'] ?? '';
    $date_expiration = $_POST['date_expiration'] ?? '';

    $verification = verifierCarte($numero_carte, $date_expiration);

    if ($verification['valide']) {
        $succes = true;
        viderPanier();
    } else {
        $erreur = $verification['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - CD Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <main class="container">
        <h2>Paiement</h2>

        <?php if ($succes): ?>
            <div class="alert success">
                <h3>✅ Paiement validé !</h3>
                <p>Votre commande a été confirmée. Merci pour votre achat !</p>
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        <?php else: ?>
            <?php if ($erreur): ?>
                <div class="alert error"><?= e($erreur) ?></div>
            <?php endif; ?>

            <div class="paiement-container">
                <div class="resume-commande">
                    <h3>Résumé de la commande</h3>
                    <table class="resume-table">
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?= e($article['titre']) ?></td>
                                <td>x<?= $article['quantite'] ?></td>
                                <td><?= formatPrix($article['prix'] * $article['quantite']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong><?= formatPrix($total) ?></strong></td>
                        </tr>
                    </table>
                </div>

                <div class="formulaire-paiement">
                    <h3>Informations de paiement</h3>
                    <form method="post">
                        <div class="form-group">
                            <label for="numero_carte">Numéro de carte (16 chiffres) :</label>
                            <input type="text"
                                   id="numero_carte"
                                   name="numero_carte"
                                   placeholder="1234 5678 9012 3451"
                                   maxlength="19"
                                   required
                                   pattern="[\d\s]{16,19}">
                            <small>Le premier et le dernier chiffre doivent être identiques</small>
                        </div>

                        <div class="form-group">
                            <label for="date_expiration">Date d'expiration (MM/AA) :</label>
                            <input type="text"
                                   id="date_expiration"
                                   name="date_expiration"
                                   placeholder="12/25"
                                   maxlength="5"
                                   required
                                   pattern="\d{2}/\d{2}">
                            <small>La carte doit être valide au moins 3 mois</small>
                        </div>

                        <div class="form-actions">
                            <a href="panier.php" class="btn btn-secondary">← Retour au panier</a>
                            <button type="submit" class="btn btn-primary btn-large">
                                Valider le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="info-test">
                <h4>🧪 Mode test - Exemples de cartes valides :</h4>
                <ul>
                    <li>1234 5678 9012 3451 (expire 12/26 ou plus tard)</li>
                    <li>5234 5678 9012 3455 (expire 06/28 ou plus tard)</li>
                    <li>9876 5432 1098 7659 (expire 03/27 ou plus tard)</li>
                </ul>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Formatage automatique du numéro de carte
        document.getElementById('numero_carte').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Formatage automatique de la date
        document.getElementById('date_expiration').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>