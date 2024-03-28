<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloc</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <h1>Réservation</h1>
    <form action="reservation.php" method="POST">
   <!-- Vérifier si l'identifiant du vélo est passé dans l'URL -->
        <?php
        if(isset($_GET['idvelo'])) {
            // Récupérer l'identifiant du vélo depuis l'URL
            $idvelo = $_GET['idvelo'];
            // Afficher l'identifiant du vélo
            echo "<input type='hidden' name='idvelo' value='$idvelo' readonly>";
        } else {
            echo "Identifiants du vélo non spécifié";
        }
        ?><br>
        <label for="date_debut">Date de début :</label>
        <input type="date" id="date_debut" name="date_debut" required><br>

        <label for="date_fin">Date de fin :</label>
        <input type="date" id="date_fin" name="date_fin" required><br>

        <button type="submit">Valider</button>
    </form>
</body>
</html>

<?php
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les identifiants du vélo et du propriétaire sont présents dans le formulaire
    if (isset($_POST['idvelo']) && isset($_POST['datedebut']) && isset($_POST['datefin'])) {
        // Récupérer les valeurs soumises dans le formulaire
        $idvelo = $_POST['idvelo'];
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];

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

        // Préparer la requête SQL d'insertion
        $sql = "INSERT INTO reservation (datedebut, datefin, idvelo, idloc) VALUES (?, ?, ?, ?)";
        
        // Préparer la déclaration SQL
        $stmt = $conn->prepare($sql);
        
        // Lier les valeurs
        $stmt->bind_param("iiss", $idvelo, $idproprio, $date_debut, $date_fin);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Réservation ajoutée avec succès.";
        } else {
            echo "Erreur lors de l'ajout de la réservation : " . $conn->error;
        }

        // Fermer la connexion
        $conn->close();
    } else {
        echo "Veuillez remplir tous les champs du formulaire.";
    }
}
?>

