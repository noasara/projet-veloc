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

// Préparation de la requête SQL pour supprimer le compte utilisateur
$sql = "DELETE FROM users WHERE id = $user_id";

// Exécution de la requête SQL
if ($conn->query($sql) === TRUE) {
    // Déconnexion de l'utilisateur
    session_unset();
    session_destroy();
    // Rediriger vers une page de confirmation ou la page d'accueil
    header("Location: confirm_suppression.php");
    exit();
} else {
    echo "Erreur lors de la suppression du compte : " . $conn->error;
}

// Fermer la connexion à la base de données
$conn->close();
?>
