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
        if(isset($_GET['idvelo'])&&isset($_GET['user_id'])){
            // Récupérer l'identifiant du vélo depuis l'URL
            $idvelo = $_GET['idvelo'];
            $user_id = $_GET['user_id'];
        }
        //     // Afficher les identifiants du vélo et de l'user
        //     echo "<input type='hidden' name='idvelo' value='$idvelo' readonly>";
        //     echo '<br>';
        //     echo "<input type='hidden' name='user_id' value='$user_id' readonly>";
        // } else {
        //     echo "Identifiant du vélo non spécifié";
        //     echo '<br>';
        //     echo "Identifiant de l'utilisateur non spécifié";
         
        
        ?><br>
        <label for="datedebut">Date de début :</label>
        <input type="date" id="datedebut" name="datedebut" required><br>

        <label for="datefin">Date de fin :</label>
        <input type="date" id="datefin" name="datefin" required><br>

        <button type="submit">Valider</button>
    </form>
</body>
</html>
<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les identifiants du vélo et du propriétaire sont présents dans le formulaire
    if (isset($_POST['datedebut']) && isset($_POST['datefin'])) {
        // Récupérer les valeurs soumises dans le formulaire
        $datedebut = $_POST['datedebut'];
        $datefin = $_POST['datefin'];
        $idvelo = $_POST['idvelo'];
        $user_id = $_POST['user_id'];
        
    
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
        // Vérifier s'il existe déjà une réservation pour le même vélo à la même date
        $sql_check_reservation = "SELECT * FROM reservation WHERE idvelo = ? AND ((datedebut <= ? AND datefin >= ?) OR (datedebut <= ? AND datefin >= ?))";
        $stmt_check_reservation = $conn->prepare($sql_check_reservation);
        $stmt_check_reservation->bind_param("issss", $idvelo, $datedebut, $datedebut, $datefin, $datefin);
        $stmt_check_reservation->execute();
        $result_check_reservation = $stmt_check_reservation->get_result();

        // Si une réservation est trouvée, afficher un message d'erreur
        if ($result_check_reservation->num_rows > 0) {
            echo "Impossible de réserver à cette date. Le vélo est déjà réservé.";
        } else {

        // Préparer la requête SQL d'insertion
        $sql = "INSERT INTO reservation (datedebut, datefin, idvelo, idloc) VALUES (?, ?, ?, ?)";
        
        // Préparer la déclaration SQL
        $stmt = $conn->prepare($sql);
        
        // Lier les valeurs
        $stmt->bind_param("ssii", $datedebut, $datefin, $idvelo, $user_id);
        
        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Réservation ajoutée avec succès.";
        } else {
            echo "Erreur lors de l'ajout de la réservation : " . $conn->error;
        }

        // Fermer la connexion
        $conn->close();
    }} else {
        echo "Veuillez remplir tous les champs du formulaire.";
    }
}
?>

