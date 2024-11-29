<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi la mail del docente da associare alla materia e l'ID della materia:
    $email = $_POST["email"];
    $IDMateria = $_POST["IDMateria"];

    //Prova ad effettuare l'assegnazione docente-materia nel database:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Associa il docente alla materia:
        $query = "UPDATE Materie SET Email_docente = ? WHERE ID = ?;";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $IDMateria]);

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