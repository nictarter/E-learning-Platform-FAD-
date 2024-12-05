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
    //Prendi la classe da inserire e le materie ad essa collegate:
    $classe = $_POST["classe"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad inserire la classe nel database e, successivamente, ad aggiungere anche tutte le materie ad essa associate:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Inserisci la classe nel database:
        $query = "INSERT INTO Classi (Classe, Anno_scolastico) VALUES (?, ?);";
        $stmt = $db->prepare($query);
        $stmt->execute([$classe, $annoScolastico]);

        //Crea la cartella della classe in cui verranno inseriti tutti i file FAD dell'anno scolastico in corso:
        mkdir("../../FAD/" . $annoScolastico . "/" . $classe);

        //Prendi ora tutte le materie del corso LOS4:
        $query = "SELECT * FROM Materie_corso;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $materie = $stmt;

        //Controlla quindi una ad una se le materie sono state selezionate per la classe da aggiungere:
        while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
            //Controlla se la materia è stata associata alla classe:
            if (isset($_POST[$materia->Materia_value])) {
                //Se la materia è stata associata alla classe, inseriscila nel database ed associala alla classe ed all'anno scolastico in corso:
                //Se la materia non è stata associata alla classe, controlla la prossima materia.
                $query = "INSERT INTO Materie (Materia, Classe, Anno_scolastico) VALUES (?, ?, ?);";
                $stmt = $db->prepare($query);
                $stmt->execute([$materia->Materia, $classe, $annoScolastico]);

                //Crea dunque la cartella della materia in cui verranno inseriti tutti i file FAD per questa classe dell'anno scolastico in corso:
                mkdir("../../FAD/" . $annoScolastico . "/" . $classe . "/" . $materia->Materia);
            }
        }
        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../gestisciClassi.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento/il ritorno dei dati nel database, dillo:
        die("L'invio/il ritorno dei dati al/dal database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../../index.php");
}