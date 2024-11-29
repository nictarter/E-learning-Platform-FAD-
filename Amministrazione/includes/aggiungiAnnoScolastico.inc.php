<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi il nuovo anno scolastico:
    $nuovoAnnoScolastico = $_POST["nuovoAnnoScolastico"];

    //Prova ad inserirlo nel database:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Imposta l'anno scolastico precedente come terminato:
        $query = "UPDATE Anno_scolastico SET Terminato = '1' WHERE Terminato = '0';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Inserisci il nuovo anno scolastico:
        $query = "INSERT INTO Anno_scolastico (Anno_scolastico, Terminato) VALUES (?, '0');";
        $stmt = $db->prepare($query);
        $stmt->execute([$nuovoAnnoScolastico]);

        //Imposta il nuovo anno scolastico nella sessione corrente:
        $_SESSION["annoScolastico"] = [$nuovoAnnoScolastico];

        //Imposta l'anno scolastico nel formato valido per il nome della tabella (XXXX_YYYY invece che XXXX-YYYY):
        $nuovoAnnoScolastico = preg_replace("/-/", "_", $nuovoAnnoScolastico);

        //Crea la tabella di rendicontazione delle ore FAD per il nuovo anno scolastico:
        $query = "CREATE TABLE RendicontoOre_" . $nuovoAnnoScolastico . " (ID int AUTO_INCREMENT, IDFAD int, Minuti int, Email_studente text, Materia text, Classe text, PRIMARY KEY (ID));";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Crea la cartella del nuovo anno scolastico in cui verranno inseriti tutti i file FAD:
        mkdir("../../FAD/" . implode($_SESSION["annoScolastico"]));

        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../annoScolastico.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../../index.php");
}