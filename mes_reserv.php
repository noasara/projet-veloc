<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: connexion.php");
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Paramètres de connexion à la base de données
$servername = "localhost"; // Nom du serveur (habituellement localhost)
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données
$database = "veloc_old"; // Nom de la base de données

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Préparation de la requête SQL pour récupérer les réservations de l'utilisateur
$sql_reservations = "SELECT reservation.*, velo.marque AS marque, velo.typ AS typ, velo.couleur AS couleur
                    FROM reservation
                    INNER JOIN velo ON reservation.idvelo = velo.id
                    WHERE idloc = $user_id";


// Exécution de la requête SQL pour les réservations
$result_reservations = $conn->query($sql_reservations);

// Vérification s'il y a des réservations
$reservations = [];
if ($result_reservations->num_rows > 0) {
    // Récupération des réservations dans un tableau
    while ($row = $result_reservations->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Mes réservations</title>
</head>
<body>
<a href="./home.php">Accueil</a>
<a href="./compte.php">Mon compte</a>
<a href="./deconnexion.php">Déconnexion</a>
<h1>Mes réservations</h1>
<?php if (!empty($reservations)) : ?>
    <ul>
        <?php foreach ($reservations as $reservation) : ?>
            <li>
                <b>ID de la réservation : <?php echo $reservation['id']; ?><br></b>
                Marque du vélo : <?php echo $reservation['marque']; ?><br>
                Type du vélo : <?php echo $reservation['typ']; ?><br>
                Couleur du vélo : <?php echo $reservation['couleur']; ?><br>
                Dates : Du <?php echo $reservation['datedebut']?> au <?php echo $reservation['datefin']; ?><br>
                 <!-- Formulaire de suppression de réservation -->
                 <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_reservation" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" name="delete_reservation">Supprimer la réservation</button>
                </form>
                <?php
                    // Vérifier si la requête de suppression de réservation a été soumise
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_reservation'])) {
                        // Récupérer l'ID de la réservation à supprimer depuis le formulaire
                        $reservation_id = $_POST['id_reservation'];

                        // Préparation de la requête SQL pour supprimer la réservation de la base de données
                        $sql_delete_reservation = "DELETE FROM reservation WHERE id = $reservation_id AND idloc = $user_id";

                        // Exécution de la requête SQL pour supprimer la réservation
                        if ($conn->query($sql_delete_reservation) === TRUE) {
                            echo "La réservation a été supprimée avec succès.";
                        } else {
                            echo "Erreur lors de la suppression de la réservation : " . $conn->error;
                        }
                    }
                ?>
            </li><br>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Vous n'avez pas encore de réservation.</p>
<?php endif; ?>
</body>
</html>
