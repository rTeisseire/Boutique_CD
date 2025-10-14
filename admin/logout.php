<?php
session_start();

// Supprimer les variables de session admin
unset($_SESSION['admin_logged']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Rediriger vers la page de connexion
header('Location: login.php');
exit;
?>