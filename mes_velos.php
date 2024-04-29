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

// Préparation de la requête SQL pour récupérer les vélos prêtés par l'utilisateur
$sql_bikes = "SELECT velo.*
              FROM velo
              WHERE idproprio = $user_id";

// Exécution de la requête SQL pour les vélos prêtés
$result_bikes = $conn->query($sql_bikes);

// Vérification s'il y a des vélos 
$bikes = [];
if ($result_bikes->num_rows > 0) {
    // Récupération des vélos ajoutés dans un tableau
    while ($row = $result_bikes->fetch_assoc()) {
        $bikes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Mes vélos</title>
</head>
<body>
<a href="./home.php">Accueil</a>
<a href="./compte.php">Mon compte</a>
<a href="./deconnexion.php">Déconnexion</a>
<h1>Mes vélos</h1>
<?php if (!empty($bikes)) : ?>
    <ul>
        <?php foreach ($bikes as $bike) : ?>
            <li>
                <b>ID du vélo : <?php echo $bike['id']; ?></b><br>
                Marque : <?php echo $bike['marque']; ?><br>
                Type : Vélo <?php echo $bike['typ']; ?><br>
                Couleur : <?php echo $bike['couleur']; ?><br>

                <!-- Formulaire de suppression -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="idvelo" value="<?php echo $bike['id']; ?>">
                    <button type="submit" name="delete_bike">Supprimer</button><br><br>
                    <?php
                        // Vérifier si la requête de suppression a été soumise et si l'ID du vélo correspond à celui en cours de traitement
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_bike']) && $_POST['idvelo'] == $bike['id']) {
                            // Récupérer l'ID du vélo à supprimer depuis le formulaire
                            $bike_id = $_POST['idvelo'];

                            // Vérifier s'il y a des réservations associées à ce vélo
                            $sql_check_reservations = "SELECT COUNT(*) as count FROM reservation WHERE idvelo = $bike_id";
                            $result_check_reservations = $conn->query($sql_check_reservations);

                            if ($result_check_reservations && $result_check_reservations->num_rows > 0) {
                                $row = $result_check_reservations->fetch_assoc();
                                $reservation_count = $row['count'];

                                // Si des réservations existent, afficher un message d'erreur et empêcher la suppression
                                if ($reservation_count > 0) {
                                    echo "<br>Ce vélo ne peut pas être supprimé car il est réservé.";
                                } else {
                            // Préparation de la requête SQL pour supprimer le vélo de la base de données
                            $sql_delete_bike = "DELETE FROM velo WHERE id = $bike_id";

                            // Exécution de la requête SQL pour supprimer le vélo
                            if ($conn->query($sql_delete_bike) === TRUE) {
                                echo "Le vélo a été supprimé avec succès.";
                            } else {
                                echo "Erreur lors de la suppression du vélo : " . $conn->error;
                            }
                        }
                                }else{
                                    echo "Erreur lors de la vérification des réservations.";
                                }}

                    ?>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Vous n'avez pas encore de vélo à prêter.</p>
<?php endif; ?>
<a href="./home.php"><button type="button">Ajouter un vélo</button></a>
</body>
</html>
