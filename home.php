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

        <!--Zone de saisie du prix du vélo -->
        <!-- <input type="text" id="prixloc" name="prixloc" pattern="[1-5]?[0-9]|60" placeholder="Entrez le prix de location de votre vélo" required> -->

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
    <?php $user_id = $_SESSION['user_id'];?>
    <?php 
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

    // Vérification si le paramètre de recherche est présent dans l'URL
    if (isset($_GET['q'])) {
        // Récupération du terme de recherche
        $search_query = $_GET['q'];

        // Préparation de la requête SQL pour rechercher les vélos
        $sql = "SELECT * FROM velo WHERE marque LIKE '%$search_query%' OR typ LIKE '%$search_query%' ";
        

        // Exécution de la requête SQL
        $result = $conn->query($sql);
        

       // Affichage des résultats de la recherche
if ($result->num_rows > 0) {
    echo "<p>Résultats de la recherche pour : <strong>$search_query</strong></p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()){
     
        $idvelo = $row['id'];
        
        echo "<li>" . $row["marque"] ." ". $row["typ"] ." ". $row["couleur"] ." " . " <button onclick=\"window.location.href='reservation.php?idvelo=$idvelo&user_id=$user_id';\">Réserver</button></li>";
        
        echo "<br>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucun résultat trouvé pour : <strong>$search_query</strong></p>";
}
     } //else {
    //     // Si le paramètre de recherche n'est pas présent dans l'URL, afficher un message d'erreur
    //     echo "<p>Aucun terme de recherche n'a été spécifié.</p>";
    // }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérification si toutes les données requises sont présentes
        if (isset($_POST["typ"], $_POST["marque"], $_POST["couleur"])) {
            // Récupération des données soumises
            $typ = $_POST["typ"];
            $marque = $_POST["marque"];
            $couleur = $_POST['couleur'];
            $user_id = $_SESSION["user_id"];

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }
        // Préparation de la requête SQL pour vérifier si le vélo existe déjà
        $sql_check_bike = "SELECT id FROM velo WHERE idproprio = ? AND marque = ? AND couleur = ? AND typ = ?";
        $stmt_check_bike = $conn->prepare($sql_check_bike);
        $stmt_check_bike->bind_param("isss", $user_id, $marque, $couleur, $typ);

        // Exécution de la requête de vérification
        $stmt_check_bike->execute();
        $stmt_check_bike->store_result();

        // Vérification du nombre de lignes résultantes
        if ($stmt_check_bike->num_rows > 0) {
            echo "<br>Un vélo similaire existe déjà !";
        } else {
        // Préparation de la requête SQL pour insérer les données dans la table velo
        $sql_ajout = "INSERT INTO velo (idproprio, marque, couleur, typ) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_ajout);
        $stmt->bind_param("isss", $user_id, $marque, $couleur, $typ);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            // Afficher une alerte en JavaScript pour informer l'utilisateur que le vélo a été ajouté avec succès
            echo "<br>Le vélo a été ajouté avec succès !";
        } else {
            echo "<br>Erreur lors de l'insertion des données : " . $conn->error;
        }

        // Fermeture du statement et de la connexion à la base de données
        $stmt->close();
        }
        // Fermeture du statement de vérification et de la connexion à la base de données
        $stmt_check_bike->close();
        
    
    // Fermeture de la connexion à la base de données
    $conn->close();
} else {
    echo "<br>Toutes les données requises n'ont pas été soumises.";
}
// } else {
// echo "Le formulaire n'a pas été soumis.";
}
    ?>
</body>
</html>
