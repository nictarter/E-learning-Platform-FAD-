<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi tutti i dati relativi alla FAD da aggiungere e l'anno scolastico in corso:
    $nomeFAD = $_POST["nome"];
    $cartellaClasseMateria = $_POST["cartellaClasseMateria"];
    $descrizione = $_POST["descrizione"];
    $minutiDaConteggiare = $_POST["daConteggiare"];
    $inizioVisibilita = $_POST["inizioVisibilita"];
    $termineConteggio = $_POST["termineConteggio"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad inserire il tutto nel database ed a caricare il file FAD nella relativa cartella (prima però controlla che il docente non abbia assegnato più di quanto possa):
    try {
        //Connettiti al database:
        include_once "connectDatabase.inc.php";

        //Prendi il monteore annuale previsto di questa materia:
        $query = "SELECT * FROM Materie WHERE Materia = '" . $cartellaClasseMateria[2] . "' AND Classe = '" . $cartellaClasseMateria[1] . "' AND Anno_scolastico = '" . $annoScolastico . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $monteoreAnnuale = $stmt->fetch();

        //Prendi ora il monteore di questa materia che è già stato assegnato:
        $query = "SELECT SUM(Minuti) FROM FAD WHERE Materia = '" . $cartellaClasseMateria[2] . "' AND Classe = '" . $cartellaClasseMateria[1] . "' AND Anno_scolastico = '" . $annoScolastico . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $monteoreAssegnato = $stmt->fetch();

        //Controlla quindi se è stato assegnato più di quanto previsto:
        if (($monteoreAnnuale - (int)$monteoreAssegnato - $minutiDaConteggiare) >= 0) {
            //Se ha assegnato più di quanto previsto, torna indietro e avvisa il docente:
            echo '<script>';
            echo 'location.replace("../aggiungiFAD.php");';
            echo 'alert("Attenzione!\nNon è stato possibile inserire la FAD per superamento monteore annuale previsto.");';
            echo 'alert("Monteore annuale: ' . $monteoreAnnuale->Monteore_minuti . '\nMonteore assegnato: ' . (int)$monteoreAssegnato . '\nMinuti da conteggiare: ' . $minutiDaConteggiare . '")';
            echo '</script>';
            die();
        }

        //Trova l'estensione del file FAD da caricare:
        $infoFile = new SplFileInfo($_FILES["file"]["name"]);
        $estensioneFile = $infoFile->getExtension();

        //Inserisci la FAD nel database:
        $query = "INSERT INTO FAD (Nome, Descrizione, Minuti, Materia, Classe, Anno_scolastico, Inizio_visibilita, Fine_conteggio, IDCartella, Estensione_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $db->prepare($query);
        $stmt->execute([$nomeFAD, $descrizione, $minutiDaConteggiare, $cartellaClasseMateria[2], $cartellaClasseMateria[1], $annoScolastico, $inizioVisibilita, $termineConteggio, $cartellaClasseMateria[0], $estensioneFile]);

        //Trova l'ID dell'ultima FAD caricata nel database(che dovrebbe corrispondere a quella appena inserita):
        $IDFAD = $db->lastInsertId();

        //Inserisci ora il file FAD nella relativa cartella, cambiandone anche il nome nell'ID FAD che è stato associato:
        $directory = $_SERVER["DOCUMENT_ROOT"] . "/FAD/" . $annoScolastico . "/" . $cartellaClasseMateria[1] . "/" . $cartellaClasseMateria[2] . "/" . $IDFAD . "." . $estensioneFile;
        move_uploaded_file($_FILES["file"]["tmp_name"], $directory);

        //Ferma tutto il processo e torna alla pagina precedente (in questo caso alla home):
        $db = null;
        $stmt = null;
        header("Location: ../index.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../index.php");
}