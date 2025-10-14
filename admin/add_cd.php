<?php
require_once '../includes/functions.php';

// Vérifier l'authentification
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$mode = 'add';
$cd = null;
$erreur = '';
$succes = '';

// Mode édition
if (isset($_GET['edit'])) {
    $mode = 'edit';
    $id = intval($_GET['edit']);
    $cd = getCDById($id);
    if (!$cd) {
        header('Location: index.php');
        exit;
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);

    // Validation
    if (empty($titre) || empty($auteur) || empty($genre) || $prix <= 0) {
        $erreur = 'Tous les champs sont obligatoires et le prix doit être positif';
    } else {
        $image_name = $cd['image'] ?? '';

        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Générer un nom unique
                $image_name = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = '../images/pochettes/' . $image_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Supprimer l'ancienne image si en mode édition
                    if ($mode === 'edit' && $cd['image'] && $cd['image'] !== 'default.png') {
                        $old_path = '../images/pochettes/' . $cd['image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                } else {
                    $erreur = 'Erreur lors de l\'upload de l\'image';
                }
            } else {
                $erreur = 'Format d\'image non autorisé (jpg, jpeg, png, gif uniquement)';
            }
        } elseif ($mode === 'add') {
            $image_name = 'default.png';
        }

        if (!$erreur) {
            $db = getDB();

            if ($mode === 'edit') {
                $stmt = $db->prepare('UPDATE cds SET titre = ?, auteur = ?, genre = ?, prix = ?, image = ? WHERE id = ?');
                $stmt->execute([$titre, $auteur, $genre, $prix, $image_name, $cd['id']]);
                $succes = 'CD modifié avec succès';
            } else {
                $stmt = $db->prepare('INSERT INTO cds (titre, auteur, genre, prix, image) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$titre, $auteur, $genre, $prix, $image_name]);
                $succes = 'CD ajouté avec succès';
            }

            // Redirection après succès
            header('Location: index.php?success=' . urlencode($succes));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Modifier' : 'Ajouter' ?> un CD - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🎵 CD Shop - Administration</h1>
            <nav>
                <span>👤 <?= e($_SESSION['admin_username']) ?></span>
                <a href="index.php">Retour à la liste</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2><?= $mode === 'edit' ? '✏️ Modifier' : '➕ Ajouter' ?> un CD</h2>

        <?php if ($erreur): ?>
            <div class="alert error"><?= e($erreur) ?></div>
        <?php endif; ?>

        <?php if ($succes): ?>
            <div class="alert success"><?= e($succes) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titre">Titre du CD * :</label>
                    <input type="text"
                           id="titre"
                           name="titre"
                           value="<?= $cd ? e($cd['titre']) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="auteur">Artiste / Groupe * :</label>
                    <input type="text"
                           id="auteur"
                           name="auteur"
                           value="<?= $cd ? e($cd['auteur']) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre musical * :</label>
                    <input type="text"
                           id="genre"
                           name="genre"
                           value="<?= $cd ? e($cd['genre']) : '' ?>"
                           placeholder="Rock, Pop, Jazz, etc."
                           required>
                </div>

                <div class="form-group">
                    <label for="prix">Prix (€) * :</label>
                    <input type="number"
                           id="prix"
                           name="prix"
                           value="<?= $cd ? $cd['prix'] : '' ?>"
                           step="0.01"
                           min="0.01"
                           required>
                </div>

                <div class="form-group">
                    <label for="image">Image de la pochette <?= $mode === 'add' ? '*' : '(laisser vide pour conserver)' ?> :</label>
                    <?php if ($cd && $cd['image']): ?>
                        <div class="current-image">
                            <p>Image actuelle :</p>
                            <img src="../images/pochettes/<?= e($cd['image']) ?>"
                                 alt="Pochette actuelle"
                                 style="max-width: 200px; border-radius: 5px;"
                                 onerror="this.src='../images/pochettes/default.png'">
                        </div>
                    <?php endif; ?>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/jpeg,image/png,image/gif,image/jpg"
                           <?= $mode === 'add' ? 'required' : '' ?>>
                    <small>Formats acceptés : JPG, JPEG, PNG, GIF</small>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-large">
                        <?= $mode === 'edit' ? '💾 Enregistrer les modifications' : '➕ Ajouter le CD' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 CD Shop - Administration</p>
        </div>
    </footer>
</body>
</html>