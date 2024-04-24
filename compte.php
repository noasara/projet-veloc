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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Mon compte</title>
</head>
<body>
<a href="./home.php">Accueil</a>
<a href="./deconnexion.php">Déconnexion</a>
<h1>Mon Compte</h1>
<a href="./mes_reserv.php">Mes réservations</a>
<a href="./mes_velos.php">Mes vélos</a>
<h2> <?php if (isset($_SESSION['user_prenom'])) 
        $prenom = $_SESSION['user_prenom'];
        echo "Bonjour $prenom";?> </h2>

<h3>Informations personnelles :</h3>
    Email : <?php if (isset($_SESSION['user_email'])) $user_email = $_SESSION['user_email']; echo $user_email; ?><br>
    Prénom : <?php if (isset($_SESSION['user_prenom'])) $user_prenom = $_SESSION['user_prenom']; echo $user_prenom; ?><br>
    Nom : <?php if (isset($_SESSION['user_nom'])) $user_nom = $_SESSION['user_nom']; echo $user_nom; ?>

    <h3>Modifier mes informations :</h3>
    <form action="compte.php" method="POST">

        <!-- Champs pour modifier le prénom -->
        <label for="email">Modifier l'adresse mail :</label>
        <input type="text" id="email" name="email" value="<?php echo $user_email; ?>" required><br>
        <label for="motdepasse">Modifier le mot de passe :</label>
        <input type="password" id="motdepasse" name="motdepasse" required><br><br>
        <div id="password-strength-messages"></div>
    

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <h3>Supprimer mon compte :</h3>
    <form action="supprimeCompte.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
        <button type="submit" name="supprimer_compte">Supprimer mon compte</button>
    </form>


<!-- Script JavaScript -->
<script>
        // Fonction pour vérifier la force du mot de passe
        const checkPassword = () => {
          // Réinitialiser les messages de force du mot de passe
          const password = document.getElementById('motdepasse');
          const passwordStrengthMessages = document.getElementById('password-strength-messages');
          passwordStrengthMessages.innerHTML = '';

          // Vérifier les conditions du mot de passe
          if (password.value.length < 8) {
            passwordStrengthMessages.innerHTML += 'Le mot de passe doit contenir au moins 8 caractères.<br>';
          }
          if (!/[a-z]/.test(password.value)) {
            passwordStrengthMessages.innerHTML += 'Le mot de passe doit contenir au moins une lettre minuscule.<br>';
          }
          if (!/[A-Z]/.test(password.value)) {
            passwordStrengthMessages.innerHTML += 'Le mot de passe doit contenir au moins une lettre majuscule.<br>';
          }
          if (!/[0-9]/.test(password.value)) {
            passwordStrengthMessages.innerHTML += 'Le mot de passe doit contenir au moins un chiffre.<br>';
          }
        };

        // Ajouter un écouteur d'événements pour le champ de mot de passe
        const passwordInput = document.getElementById('motdepasse');
        passwordInput.addEventListener('input', checkPassword);
      </script>

<?php

// Récupération des données du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    // Définir une variable de drapeau pour vérifier si la modification a réussi
    $modification_reussie = false;

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
    $sql = "UPDATE users SET email='$email', motdepasse='$mot_de_passe_hash' WHERE id = '{$_SESSION['user_id']}' ";

    if ($conn->query($sql) === TRUE) {
        // Mettre à jour les données de session avec les nouvelles informations
        $_SESSION['user_email'] = $email;
        // Définir la variable de drapeau comme true pour indiquer que la modification a réussi
        $modification_reussie = true;
        if ($modification_reussie === true) echo "<br>Vos informations ont bien été modifiées !";
    } else {
        echo "Vos informations n'ont pu être modifiées : " . $conn->error;
    }  
    }
    else {
        echo "Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule et un chiffre.";
    }
}
// Fermeture de la connexion
    $conn->close();?>
</body>
</html>