<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloc</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <h1>Bienvenue</h1>

    <b>Vous êtes à la recherche d'un vélo ?</b> <br><br>
    <form action="./home.php" method="GET">
        <label for="search">Rechercher :</label>
        <input type="text" id="search" name="q" placeholder="Entrez votre recherche..." required>
        <button type="submit">Rechercher</button>
    </form>
    <br>
    <b>Vous souhaitez louer votre vélo ?</b> <br><br>
    <form action="./traitement.php" method="POST">
        <label for="select-type">Ajoutez votre vélo :</label>
        <select id="type" name="type" >
            <option value="">--Type de votre vélo--</option>
            <option value="route">Velo route</option>
            <option value="vtt">VTT</option>
            <option value="urbain">Velo ville</option>
            <option value="electrique">Velo electrique</option>
            <option value="course">Velo course</option>
        </select>

        <select id="marque" name="marque" >
            <option value="">--Marque de votre vélo--</option>
            <option value="btwin">Btwin</option>
            <option value="triban">Triban</option>
            <option value="scott">Scott</option>
            <option value="BMC">BMC</option>
            
        </select>

        <button type="submit">Soumettre</button>
    </form>

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
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row["marque"] ." ". $row["modele"]. " ".$row["prixloc"] . " euros". "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Aucun résultat trouvé pour : <strong>$search_query</strong></p>";
        }
    } else {
        // Si le paramètre de recherche n'est pas présent dans l'URL, afficher un message d'erreur
        echo "<p>Aucun terme de recherche n'a été spécifié.</p>";
    }

    // Fermeture de la connexion à la base de données
    $conn->close();
    ?>
</body>
</html>
