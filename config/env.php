<?php
/**
 * Chargeur de fichier .env
 * Lit le fichier .env et charge les variables d'environnement
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        die('Erreur : Le fichier .env est introuvable. Copiez .env.example vers .env et configurez vos paramètres.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Séparer la clé et la valeur
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Retirer les guillemets si présents
            $value = trim($value, '"\'');

            // Définir la variable d'environnement
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

/**
 * Récupérer une variable d'environnement
 */
function env($key, $default = null) {
    $value = getenv($key);

    if ($value === false) {
        $value = $_ENV[$key] ?? $default;
    }

    return $value;
}

// Charger le fichier .env
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);
?>