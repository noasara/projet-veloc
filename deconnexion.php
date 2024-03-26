<?php
// Démarre une nouvelle session ou reprend la session existante
session_start();

// Détruit toutes les données de la session
session_destroy();

// Redirige l'utilisateur vers la page de connexion ou toute autre page souhaitée après la déconnexion
header("Location: connexion.html");
exit();
?>
