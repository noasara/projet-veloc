<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    <form id="inscription-form" action="./inscription.php" method="POST">
        <div>
          <label for="nom">Nom :</label>
          <input type="text" id="nom" name="nom" required>
        </div>
        <div>
          <label for="prenom">Prénom :</label>
          <input type="text" id="prenom" name="prenom" required>
        </div>
        <div>
          <label for="email">Email :</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div>
          <label for="motdepasse">Mot de passe :</label>
          <input type="password" id="motdepasse" name="motdepasse" required>
          <br>
          <div id="password-strength-messages"></div>
        </div>
        <div>
          <button type="submit">S'inscrire</button>
        </div>
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
</body>
</html>
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



