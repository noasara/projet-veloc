<?php
// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si le champ "types" est défini et non vide
    if (isset($_POST["types"]) && !empty($_POST["types"])) {
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "veloc_old";
        $conn = new mysqli($servername, $username, $password, $database);

        // Vérification de la connexion
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        // Récupération des valeurs soumises du champ "types" (tableau)
        $types = $_POST["types"];

        // Préparation de la requête SQL pour insérer les choix multiples dans la base de données
        $sql = "INSERT INTO velo VALUES (idproprio, marque, prixloc, typ)";
        $values = array();
        foreach ($types as $type) {
            $values[] = "('" . $conn->real_escape_string($type) . "')";
        }
        $sql .= implode(", ", $values);

        // Exécution de la requête SQL
        if ($conn->query($sql) === TRUE) {
            echo "Les types de vélo ont été ajoutés avec succès.";
        } else {
            echo "Erreur lors de l'ajout des types de vélo : " . $conn->error;
        }

        // Fermeture de la connexion à la base de données
        $conn->close();
    } else {
        echo "Aucun type de vélo sélectionné.";
    }
} else {
    echo "Le formulaire n'a pas été soumis.";
}
?>
