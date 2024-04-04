<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Veloc - Confirmation</title>
</head>
<body>
<a href="./home.php">Accueil</a>
<a href="./deconnexion.php">Déconnexion</a>

<h2>Réservation n°<?php if (isset($_SESSION['id_reservation'])) $id_reservation = $_SESSION['id_reservation']; echo $id_reservation; ?></h2>
</body>
</html>

<?php
// Vérifier si l'ID de réservation est défini en session
if (isset($_SESSION['id_reservation'])) {
    // Récupérer l'ID de réservation depuis la session
    $id_reservation = $_SESSION['id_reservation'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "veloc_old";
    $conn = new mysqli($servername, $username, $password, $database);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Préparer la requête SQL pour récupérer les détails de la réservation
    $sql = "SELECT velo.marque, velo.typ, velo.couleur, reservation.datedebut, reservation.datefin FROM velo JOIN reservation ON velo.id = reservation.idvelo WHERE reservation.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_reservation);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier s'il y a des résultats
    if ($result->num_rows > 0) {
        // Afficher les détails de la réservation
        $row = $result->fetch_assoc();
        $marque = $row['marque'];
        $type = $row['typ'];
        $couleur = $row['couleur'];
        $datedebut = $row['datedebut'];
        $datefin = $row['datefin'];
    // Afficher les détails dans la page HTML
    echo "Votre réservation a bien été prise en compte !<br><br>";
    echo "Récapitulatif :<br>";
    echo "Réservation n° : $id_reservation<br>";
    echo "Marque du vélo : $marque<br>";
    echo "Type : $type<br>";
    echo "Couleur : $couleur<br>";
    echo "Dates : Du $datedebut au $datefin<br>";

    } else {
    echo "Aucune réservation trouvée avec l'identifiant fourni.";
    }
        //Création d'un pdf téléchargeable
        $contenu_recap = "Récapitulatif de la réservation :\n";
        $contenu_recap .= "N° de réservation : $id_reservation\n";
        $contenu_recap .= "Marque du vélo : $marque\n";
        $contenu_recap .= "Type : $type\n";
        $contenu_recap .= "Couleur : $couleur\n";
        $contenu_recap .= "Dates de réservation : Du $datedebut au $datefin\n";

        // Nom du fichier temporaire
        $nom_fichier = "recap_reservation_$id_reservation.pdf";

        // Chemin du fichier temporaire
        $chemin_fichier = "./$nom_fichier";

        // Écrire le contenu du récapitulatif dans un fichier texte temporaire sur le serveur
        file_put_contents($chemin_fichier, $contenu_recap);

        // Fournir un lien de téléchargement vers le fichier texte temporaire
        echo "<p>Téléchargez ci-dessus le récapitulatif de votre réservation :</p>";
        echo "<a href='$chemin_fichier' download='recap_reservation_$id_reservation.pdf'>Télécharger</a>";
    }
        

    // Fermer la connexion à la base de données
    $conn->close();
//} //else {
    //echo "Aucun identifiant de réservation n'est défini.";
//}
?>
