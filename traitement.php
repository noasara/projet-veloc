<?php
session_start();
// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si toutes les données requises sont présentes
    if (isset($_POST["typ"], $_POST["marque"], $_POST["prixloc"])) {
        // Récupération des données soumises
        $typ = $_POST["typ"];
        $marque = $_POST["marque"];
    
        $prixloc = $_POST["prixloc"];
        $user_id = $_SESSION["user_id"];

        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "veloc_old";
        $conn = new mysqli($servername, $username, $password, $database);

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        // Préparation de la requête SQL pour insérer les données dans la table velo
        $sql = "INSERT INTO velo (idproprio, marque, prixloc, typ) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $user_id, $marque, $prixloc, $typ);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            // Afficher une alerte en JavaScript pour informer l'utilisateur que le vélo a été ajouté avec succès
            echo "Le vélo a été ajouté avec succès !";
            
        } else {
            echo "Erreur lors de l'insertion des données : " . $conn->error;
        }

        // Fermeture du statement et de la connexion à la base de données
        $stmt->close();
        $conn->close();
    } else {
        echo "Toutes les données requises n'ont pas été soumises.";
    }
} else {
    echo "Le formulaire n'a pas été soumis.";
}
?>
<br>
<a href="./home.php">Retourner sur la page d'accueil ?</a>