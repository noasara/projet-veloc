<?php
session_start();

// Afficher le message d'erreur 
echo "Impossible de réserver à cette date. Le vélo est déjà réservé ou bien vous avez déjà une réservation aux dates selectionnées.";

// Vérifier si les valeurs de session idvelo et user_id existent
if (isset($_GET['idvelo']) && isset($_GET['user_id'])) {
    // Récupérer les valeurs de session
    $idvelo = $_GET['idvelo'];
    $user_id = $_GET['user_id'];
    // Afficher un lien pour retourner au formulaire de réservation avec les valeurs préremplies
    echo '<br><br><a href="reservation.php?idvelo=' . $idvelo . '&user_id=' . $user_id . '">Retour à la page de réservation</a>';
} else {
    // Si les valeurs de session ne sont pas définies, afficher un message d'erreur générique
    echo "Une erreur s'est produite. Veuillez réessayer.";
}
?>
