<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloc - Accueil</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <a href="./compte.php">Mon compte</a>
    <a href="./deconnexion.php">Déconnexion</a>

    <h1> <?php if (isset($_SESSION['user_prenom'])) 
        $prenom = $_SESSION['user_prenom'];
        echo "Bonjour $prenom";?> </h1>

    <b>Vous êtes à la recherche d'un vélo ?</b> <br><br>
    <form action="./home.php" method="GET">
        <label for="search">Rechercher :</label>
        <input type="text" id="search" name="q" placeholder="Entrez votre recherche..." required>
        <button type="submit">Rechercher</button>
    </form>
    <br>
    <?php 
    // Vérification si le paramètre de recherche est présent dans l'URL
    if (isset($_GET['q'])) {
        // Récupération du terme de recherche
        $search_query = $_GET['q'];

        // Récupération de l'ID de l'utilisateur connecté
        $user_id = $_SESSION['user_id'];

        // Paramètres de connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "veloc_old";

        // Connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $database);

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        // Préparation de la requête SQL pour rechercher les vélos (en excluant les vélos de l'utilisateur connecté)
        $sql = "SELECT * FROM velo WHERE (marque LIKE '%$search_query%' OR typ LIKE '%$search_query%') AND idproprio <> $user_id";

        // Exécution de la requête SQL
        $result = $conn->query($sql);

        // Affichage des résultats de la recherche
        if ($result->num_rows > 0) {
            echo "<p>Résultats de la recherche pour : <strong>$search_query</strong></p>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                $idvelo = $row['id'];
                echo "<li>" . $row["marque"] ." ". $row["typ"] ." ". $row["couleur"] ." " . " <button onclick=\"window.location.href='reservation.php?idvelo=$idvelo&user_id=$user_id';\">Réserver</button></li>";
                echo "<br>";
            }
            echo "</ul>";
        } else {
            echo "<p>Aucun résultat trouvé pour : <strong>$search_query</strong></p>";
        }
    }
    ?>
    <br>
    <b>Vous souhaitez prêter votre vélo ?</b> <br><br>
    <form action="./home.php" method="POST">
        <label for="select-type">Ajoutez votre vélo :</label>
        
        <select id="marque" name="marque" >
            <option value="">--Marque de votre vélo--</option>
            <option value="btwin">Btwin</option>
            <option value="triban">Triban</option>
            <option value="scott">Scott</option>
            <option value="BMC">BMC</option>
        </select>

        <select id="type" name="typ" >
            <option value="">--Type de votre vélo--</option>
            <option value="route">Velo route</option>
            <option value="vtt">VTT</option>
            <option value="urbain">Velo ville</option>
            <option value="electrique">Velo electrique</option>
            <option value="course">Velo course</option>
        </select>

        <input type="text" id="couleur" name="couleur" placeholder="Entrez la couleur du vélo" required>

        <button type="submit">Ajouter</button>
    </form>
    <?php

     // Paramètres de connexion à la base de données
     $servername = "localhost";
     $username = "root";
     $password = "";
     $database = "veloc_old";

     // Connexion à la base de données
     $conn = new mysqli($servername, $username, $password, $database);
    // Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}
// Récupération de l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Traitement des données du formulaire après la connexion réussie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs du formulaire
    $marque = $_POST["marque"];
    $typ = $_POST["typ"];
    $couleur = $_POST["couleur"];

    // Préparer et exécuter la requête SQL d'insertion
    $sql = "INSERT INTO velo (idproprio, marque, typ, couleur) VALUES ('$user_id','$marque', '$typ', '$couleur')";
    
    

// Vérification du résultat
if ($conn->query($sql) === TRUE) {

    //Redirection 
    header("Location: ok.php");
} else {
    // Si l'insertion a échoué, afficher un message d'erreur
    echo "Erreur lors de l'ajout du vélo : " . $conn->error;
}
}

// Fermer la connexion à la base de données
$conn->close();
?>
</body>
</html>
