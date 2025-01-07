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
    //Prendi il file CSV che è stato caricato e l'anno scolastico in corso:
    $fileCSV = $_FILES["datiClassiCSV"]["tmp_name"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad inserire i dati contenuti nel file CSV nel database:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Prendi il contenuto del file CSV:
        $file = fopen($fileCSV, "r");

        //Controlla il contenuto di ogni riga, inserendo i dati nel database se si tratta di un dato e saltando se si tratta dell'heading (alla fine del file fermati):
        while (!feof($file)) {
            if ($riga = fgetcsv($file, 0, ";") and $riga[0] !== "Classe") {
                //Inserisci i dati nel database:
                $query = "UPDATE Materie SET Email_docente = ?, Monteore_minuti = ? WHERE Materia = ? AND Classe = ? AND Anno_scolastico = '" . $annoScolastico . "';";
                $stmt = $db->prepare($query);
                $stmt->execute([$riga[2], $riga[3], $riga[1], $riga[0]]);
            }
        }

        //Chiudi il file, ferma tutto il processo e torna alla pagina precedente:
        fclose($file);
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