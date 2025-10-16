<?php
require_once '../include_admin.php';

// Vérifier l'authentification
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$cds = getAllCDs();
$message = '';

// Gestion de la suppression
if (isset($_GET['supprimer']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $cd = getCDById($id);

    if ($cd) {
        $db = getDB();
        $stmt = $db->prepare('DELETE FROM cds WHERE id = ?');
        if ($stmt->execute([$id])) {
            // Supprimer l'image si elle existe
            $image_path = '../images/pochettes/' . $cd['image'];
            if (file_exists($image_path) && $cd['image'] !== 'default.png') {
                unlink($image_path);
            }
            $message = 'CD supprimé avec succès';
            header('Location: index.php?success=' . urlencode($message));
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $message = $_GET['success'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - CD Shop</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>
    <main class="container">
        <div class="admin-header">
            <h2>Gestion des CD</h2>
            <a href="add_cd.php" class="btn btn-primary">Ajouter un CD</a>
        </div>

        <?php if ($message): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3><?= count($cds) ?></h3>
                <p>CD au catalogue</p>
            </div>
            <div class="stat-card">
                <h3><?= formatPrix(array_sum(array_column($cds, 'prix'))) ?></h3>
                <p>Valeur totale du stock</p>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pochette</th>
                    <th>Titre</th>
                    <th>Artiste</th>
                    <th>Genre</th>
                    <th>Prix</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cds as $cd): ?>
                    <tr>
                        <td><?= $cd['id'] ?></td>
                        <td>
                            <img src="../images/pochettes/<?= e($cd['image']) ?>"
                                 alt="<?= e($cd['titre']) ?>"
                                 class="admin-thumb"
                                 onerror="this.src='../images/pochettes/default.png'">
                        </td>
                        <td><?= e($cd['titre']) ?></td>
                        <td><?= e($cd['auteur']) ?></td>
                        <td><?= e($cd['genre']) ?></td>
                        <td><?= formatPrix($cd['prix']) ?></td>
                        <td><?= date('d/m/Y', strtotime($cd['date_ajout'])) ?></td>
                        <td>
                            <a href="add_cd.php?edit=<?= $cd['id'] ?>" class="btn btn-secondary btn-small">
                                Modifier
                            </a>
                            <a href="?supprimer=1&id=<?= $cd['id'] ?>"
                               class="btn btn-danger btn-small"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce CD ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>