<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi l'ID della FAD da cancellare e l'anno scolastico in corso:
    $IDFAD = $_POST["IDFAD"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova a cancellare la FAD dal database:
    try {
        //Connettiti al database:
        require_once "connectDatabase.inc.php";

        //Cancella la FAD dal database:
        $query = "DELETE FROM FAD WHERE ID='" . $IDFAD . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Cancella tutti i dati rendicontati dagli studenti per questa FAD:
        $query = "DELETE FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE IDFAD = '" . $IDFAD . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../leTueFAD.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../index.php");
}