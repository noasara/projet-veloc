<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloc - Connexion</title>
</head>
<body>
    <h1>Connexion</h1>

    <form id="connexion-form" action="./connexion.php" method="POST">
        <div>
          <label for="email">Email :</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div>
          <label for="motdepasse">Mot de passe :</label>
          <input type="password" id="motdepasse" name="motdepasse" required>
        </div>
        <div>
          <button type="submit">Se connecter</button>
        </div>
      </form>

</body>
</html>
<?php
// Connexion à la base de données
$servername = "localhost"; // Adresse du serveur MySQL (généralement localhost)
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$database = "veloc_old"; // Nom de la base de données

// Création d'une connexion
$conn = new mysqli($servername, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Configuration des caractères UTF-8 pour éviter les problèmes d'encodage
$conn->set_charset("utf8mb4");

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les champs email et mot de passe sont définis
    if (isset($_POST['email']) && isset($_POST['motdepasse'])) {
        // Récupérer les valeurs des champs
        $email = $_POST['email'];
        $mot_de_passe_hash = $_POST['motdepasse'];

        // Préparer une requête SQL pour récupérer l'utilisateur correspondant à l'email fourni
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Utilisateur trouvé, vérifier le mot de passe
            $row = $result->fetch_assoc();
            if (password_verify($mot_de_passe_hash, $row['motdepasse'])) {
                // Mot de passe correct, démarrer une session et rediriger l'utilisateur
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_prenom'] = $row['prenom'];
                $_SESSION['user_nom'] = $row['nom'];
                // Rediriger vers la page d'accueil ou toute autre page désirée
                header("Location: home.php");

                exit();
            } else {
                // Mot de passe incorrect
                $erreur_message = "Mot de passe incorrect.";
                echo "Mot de passe incorrect.";
            }
        } else {
            // Aucun utilisateur trouvé avec cet email
            $erreur_message = "Aucun utilisateur trouvé avec cet email.";
            echo "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        // Les champs email et mot de passe ne sont pas définis
        $erreur_message = "Veuillez saisir votre email et votre mot de passe.";
        echo "Veuillez saisir votre email et votre mot de passe.";
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

