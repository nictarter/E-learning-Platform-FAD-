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
    //Prendi tutti i dati relativi alla FAD da aggiungere (alcuni dati potrebbero non servire per l'inserimento nel database, ma serviranno per poter poi ritornare con metodo POST alla visualizzazione della FAD), l'email dello studente e l'anno scolastico in corso:
    $IDFAD = $_POST["IDFAD"];
    $tipoVisualizzazione = $_POST["tipoVisualizzazione"];
    $materia = $_POST["materia"];
    $classe = $_POST["classe"];
    $estensioneFile = $_POST["estensioneFile"];
    $conteggioDaAccreditare = $_POST["tempoDaConteggiare"];
    $email = $_SESSION["email"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad inserire la rendicontazione delle ore effettuate nel database:
    try {
        //Connettiti al database:
        require_once "connectDatabase.inc.php";

        //Controlla se lo studente ha mai rendicontato prima ore per questa FAD:
        $query = "SELECT * FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE IDFAD = '" . $IDFAD . "' AND Email_studente = '" . $email . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $rendicontazione = $stmt->fetch();

        //Se lo studente non ha mai rendicontato prima per questa FAD, crea il dato ed inserisci il valore rendicontato, altrimenti somma il "da rendicontare" a quanto già rendicontato:
        if ($rendicontazione === false) {
            //Inserisci la nuova rendicontazione nel database:
            $query = "INSERT INTO RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " (IDFAD, Minuti, Email_studente, Materia, Classe) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$IDFAD, $conteggioDaAccreditare, $email, $materia, $classe]);
        } else {
            //Aggiorna la precedente rendicontazione aggiungendo il nuovo conteggio:
            $query = "UPDATE RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " SET Minuti = Minuti + " . $conteggioDaAccreditare . " WHERE IDFAD = '" . $IDFAD . "' AND Email_studente = '" . $email . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();
        }

        //Form nascosto per permettere il ritorno con modalità POST alla visualizzazione FAD:
        echo '<form action="../visualizzaFAD.php" id="formRitornoFAD' . $IDFAD . '" method="post">';
        echo '<input type="hidden" name="tipoVisualizzazione" id="tipoVisualizzazione' . $tipoVisualizzazione . '" value="' . $tipoVisualizzazione . '" required>';
        echo '<input type="hidden" name="IDFAD" value="' . $IDFAD . '" required>';
        echo '<input type="hidden" name="materia" value="' . $materia . '" required>';
        echo '<input type="hidden" name="classe" value="' . $classe . '" required>';
        echo '<input type="hidden" name="estensioneFile" value="' . $estensioneFile . '" required>';
        echo '</form>';

        //Ferma tutto il processo ed invia il form nascosto tramite la funzione javascript per tornare alla FAD:
        $db = null;
        $stmt = null;
        echo '<script>';
        echo 'document.getElementById("formRitornoFAD' . $IDFAD . '").submit();';
        echo '</script>';
        die();
    } catch (PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../index.php");
}