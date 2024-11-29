<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi il monteore annuale (in minuti) della materia e l'ID della materia:
    $monteOreAnnuale = $_POST["monteOreAnnuale"];
    $IDMateria = $_POST["IDMateria"];

    //Prova ad effettuare l'assegnazione del monteore (in minuti) alla materia:
    try {
        //Connettiti al database:
        require_once "../../includes/connectDatabase.inc.php";

        //Associa il monteore alla materia:
        $query = "UPDATE Materie SET Monteore_minuti = ? WHERE ID = ?;";
        $stmt = $db->prepare($query);
        $stmt->execute([$monteOreAnnuale, $IDMateria]);

        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../gestisciClassi.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../../index.php");
}