<?php
require_once __DIR__ . '/env.php';

function getDB() {
    try {
        $pdo = new PDO(
            'mysql:host=' . $_ENV["DB_HOST"] . ';port=' . $_ENV["DB_PORT"] . ';dbname=' . $_ENV["DB_NAME"] . ';charset=utf8mb4',
            $_ENV["DB_USER"],
            $_ENV["DB_PASS"],
        );
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
}
?>