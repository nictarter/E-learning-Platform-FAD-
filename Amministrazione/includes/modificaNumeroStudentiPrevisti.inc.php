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
    //Prendi la classe, il numero di studenti previsti desiderato e l'anno scolastico in corso:
    $classe = $_POST["classe"];
    $numeroStudentiPrevistiDesiderato = $_POST["numeroStudentiPrevistiDesiderato"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad effettuare il cambio di studenti previsti desiderato per la classe:
    try {
        //Connettiti al database:
        require_once "../../includes/connectDatabase.inc.php";

        //Inserisci il nuovo numero di studenti previsti:
        $query = "UPDATE Classi SET Numero_studenti = ? WHERE Classe = ? AND Anno_scolastico = '" . $annoScolastico . "';";
        $stmt = $db->prepare($query);
        $stmt->execute([$numeroStudentiPrevistiDesiderato, $classe]);

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