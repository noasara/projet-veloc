<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Votre compte</title>
</head>
<body>
<a href="./deconnexion.php">Déconnexion</a>
<a href="./home.php">Accueil</a>
<h1>Votre Compte</h1>
<h2> <?php if (isset($_SESSION['user_prenom'])) 
        $prenom = $_SESSION['user_prenom'];
        echo "Bonjour $prenom";?> </h2>

<h3>Informations personnelles :</h3>
    Email : <?php if (isset($_SESSION['user_email'])) $user_email = $_SESSION['user_email']; echo $user_email; ?><br>
    Prénom : <?php if (isset($_SESSION['user_prenom'])) $user_prenom = $_SESSION['user_prenom']; echo $user_prenom; ?><br>
    Nom : <?php if (isset($_SESSION['user_nom'])) $user_nom = $_SESSION['user_nom']; echo $user_nom; ?>

</body>
</html>