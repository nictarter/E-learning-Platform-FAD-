<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi l'ID dello studente da rimuovere:
    $IDStudente = $_POST["IDStudente"];

    //Prova ad effettuare la cancellazione nel database:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Rimuovi lo studente dal database:
        $query = "DELETE FROM Studenti WHERE ID = '" . $IDStudente . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../gestisciStudenti.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../../index.php");
}