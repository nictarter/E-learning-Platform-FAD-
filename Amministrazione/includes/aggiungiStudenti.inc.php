<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi la mail dello studente, la classe e l'anno scolastico nel quale viene aggiunto:
    $email = $_POST["email"];
    $classe = $_POST["classe"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad effettuare le operazioni con il database:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Inserisci lo studente nel database:
        $query = "INSERT INTO Studenti (Email, Classe, Anno_scolastico) VALUES (?, ?, ?);";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $classe, $annoScolastico]);

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