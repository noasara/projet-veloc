<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloc - Réservation</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<a href="home.php">Accueil</a>
<a href="compte.php">Mon compte</a>
    <h1>Réservation</h1>
    <form action="reservation.php" method="POST">
    <!-- Champs cachés pour les identifiants -->
        <input type="hidden" name="idvelo" value="<?php echo isset($_GET['idvelo']) ? $_GET['idvelo'] : ''; ?>">
        <input type="hidden" name="user_id" value="<?php echo isset($_GET['user_id']) ? $_GET['user_id'] : ''; ?>">

        <label for="datedebut">Date de début :</label>
        <input type="date" id="datedebut" name="datedebut" min="<?php echo date('Y-m-d'); ?>" required><br>

        <label for="datefin">Date de fin :</label>
        <input type="date" id="datefin" name="datefin" min="<?php echo date('Y-m-d'); ?>" required><br><br>

        <button type="submit">Valider</button>
    </form>
</body>
</html>
<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les identifiants du vélo et du propriétaire sont présents dans le formulaire
    if (isset($_POST['datedebut']) && isset($_POST['datefin']) && isset($_POST['idvelo']) && isset($_POST['user_id'])) {
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
        $sql_check_reservation = "SELECT * FROM reservation WHERE idvelo = ? AND ((datedebut BETWEEN ? AND ?) OR (datefin BETWEEN ? AND ?))";
        $stmt_check_reservation = $conn->prepare($sql_check_reservation);
        $stmt_check_reservation->bind_param("issss", $idvelo, $datedebut, $datefin, $datedebut, $datefin);
        $stmt_check_reservation->execute();
        $result_check_reservation = $stmt_check_reservation->get_result();

        // Vérifier s'il existe déjà une réservation pour l'utilisateur aux mêmes dates
        $sql_check_user_reservation = "SELECT * FROM reservation WHERE idloc = ? AND ((datedebut BETWEEN ? AND ?) OR (datefin BETWEEN ? AND ?))";
        $stmt_check_user_reservation = $conn->prepare($sql_check_user_reservation);
        $stmt_check_user_reservation->bind_param("issss", $user_id, $datedebut, $datefin, $datedebut, $datefin);
        $stmt_check_user_reservation->execute();
        $result_check_user_reservation = $stmt_check_user_reservation->get_result();

        // Récupérer les valeurs de idvelo et user_id
        $idvelo = $_POST['idvelo'];
        $user_id = $_POST['user_id'];

        // Si une réservation est trouvée, afficher un message d'erreur
        if (($result_check_reservation->num_rows > 0)||($result_check_user_reservation->num_rows > 0)) {
            header("Location:error_message.php?idvelo=$idvelo&user_id=$user_id");         
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
            // Après avoir inséré la réservation avec succès
            $id_reservation = $conn->insert_id; // Récupérer l'ID de la réservation nouvellement insérée
            $_SESSION['id_reservation'] = $id_reservation; // Stocker l'ID de réservation dans une session

            // Redirection vers une autre page après la validation de la réservation
            header("Location: confirm_reservation.php?idvelo=$idvelo&user_id=$user_id");
            exit(); //Assure que le code suivant ne sera pas exécuté après la redirection
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

