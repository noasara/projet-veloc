<?php
// Paramètres de connexion à la base de données
$servername = "localhost"; // Nom du serveur (habituellement localhost)
$username = "root"; // Nom d'utilisateur de la base de données
$password = ""; // Mot de passe de la base de données
$database = "veloc_old"; // Nom de la base de données

// Récupération des données du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    // Vérification de la force du mot de passe
    if (strlen($motdepasse) <= 8 && preg_match("/[a-z]/", $motdepasse) && preg_match("/[A-Z]/", $motdepasse) && preg_match("/[0-9]/", $motdepasse)) {
        
        // Hashage du mot de passe (utilisation de l'algorithme de hachage bcrypt)
    $mot_de_passe_hash = password_hash($motdepasse, PASSWORD_DEFAULT);

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $database);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Préparation de la requête SQL pour insérer un nouvel utilisateur dans la table
    $sql = "INSERT INTO users (nom, prenom, email, motdepasse) VALUES ('$nom', '$prenom', '$email', '$mot_de_passe_hash')";

    if ($conn->query($sql) === TRUE) {
        ?>
        <html>
        <p>Vous êtes bien inscrit !</p>
        <a href="connexion.html">Vous souhaitez vous connecter ?</a>
        </html>
        <?php
    } else {
        echo "Erreur lors de l'inscription : " . $conn->error;
    }

    // Fermeture de la connexion
    $conn->close();  
    } else {
// Rediriger vers la page d'accueil ou toute autre page désirée
// header("Location: veloc.html");
echo "Erreur inscription";
    }
    
}
?>
