<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Initialiser le panier si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Récupérer tous les CD
function getAllCDs() {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM cds ORDER BY date_ajout DESC');
    return $stmt->fetchAll();
}

// Récupérer un CD par son ID
function getCDById($id) {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM cds WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Ajouter un CD au panier
function ajouterAuPanier($id_cd) {
    if (isset($_SESSION['panier'][$id_cd])) {
        $_SESSION['panier'][$id_cd]++;
    } else {
        $_SESSION['panier'][$id_cd] = 1;
    }
}

// Retirer un CD du panier
function retirerDuPanier($id_cd) {
    if (isset($_SESSION['panier'][$id_cd])) {
        unset($_SESSION['panier'][$id_cd]);
    }
}

// Vider le panier
function viderPanier() {
    $_SESSION['panier'] = [];
}

// Calculer le total du panier
function calculerTotal() {
    $total = 0;
    if (!empty($_SESSION['panier'])) {
        $db = getDB();
        foreach ($_SESSION['panier'] as $id_cd => $quantite) {
            $cd = getCDById($id_cd);
            if ($cd) {
                $total += $cd['prix'] * $quantite;
            }
        }
    }
    return $total;
}

// Récupérer les articles du panier
function getPanierDetails() {
    $articles = [];
    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $id_cd => $quantite) {
            $cd = getCDById($id_cd);
            if ($cd) {
                $cd['quantite'] = $quantite;
                $articles[] = $cd;
            }
        }
    }
    return $articles;
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

// Vérifier la carte de crédit
function verifierCarte($numero, $date_expiration) {
    // Vérifier que le numéro contient 16 chiffres
    $numero = preg_replace('/\s+/', '', $numero);
    if (!preg_match('/^\d{16}$/', $numero)) {
        return ['valide' => false, 'message' => 'Le numéro de carte doit contenir 16 chiffres'];
    }

    // Vérifier que le dernier chiffre est identique au premier
    if ($numero[0] !== $numero[15]) {
        return ['valide' => false, 'message' => 'Le dernier chiffre doit être identique au premier'];
    }

    // Vérifier la date d'expiration (doit être > date du jour + 3 mois)
    $date_min = new DateTime();
    $date_min->modify('+3 months');

    try {
        $parts = explode('/', $date_expiration);
        if (count($parts) !== 2) {
            return ['valide' => false, 'message' => 'Format de date invalide (utilisez MM/AA)'];
        }

        $mois = intval($parts[0]);
        $annee = intval('20' . $parts[1]);

        if ($mois < 1 || $mois > 12) {
            return ['valide' => false, 'message' => 'Mois invalide'];
        }

        $date_carte = new DateTime("$annee-$mois-01");
        $date_carte->modify('last day of this month');

        if ($date_carte < $date_min) {
            return ['valide' => false, 'message' => 'La carte doit être valide au moins 3 mois'];
        }

        return ['valide' => true, 'message' => 'Carte valide'];
    } catch (Exception $e) {
        return ['valide' => false, 'message' => 'Date d\'expiration invalide'];
    }
}

// Sécuriser l'affichage HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Formater le prix
function formatPrix($prix) {
    return number_format($prix, 2, ',', ' ') . ' €';
}
?>