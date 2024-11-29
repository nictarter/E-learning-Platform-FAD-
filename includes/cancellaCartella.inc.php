<?php

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi l'ID della FAD da cancellare e l'anno scolastico in corso:
    $IDCartella = $_POST["IDCartella"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova a cancellare la FAD dal database:
    try {
        //Connettiti al database:
        require_once "connectDatabase.inc.php";

        //Cancella la cartella dal database:
        $query = "DELETE FROM Cartelle WHERE ID = '" . $IDCartella . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        //Prendi tutte le FAD contenute all'interno di questa cartella:
        $query = "SELECT * FROM FAD WHERE IDCartella = '" . $IDCartella . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $FADsCartella = $stmt;

        //Cancella tutte le FAD contenute all'interno di questa cartella e tutti i dati rendicontati dagli studenti per ognuna:
        while ($FADCartella = $FADsCartella->fetch(PDO::FETCH_OBJ)) {
            //Cancella la FAD dal database:
            $query = "DELETE FROM FAD WHERE ID = '" . $FADCartella->ID . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();

            //Cancella ora i dati rendicontati dagli studenti per questa FAD:
            $query = "DELETE FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE IDFAD = '" . $FADCartella->ID . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();
        }

        //Prendi ora tutte le sottocartelle contenute all'interno di questa cartella:
        $query = "SELECT * FROM Cartelle WHERE ID_Cartella_Principale = '" . $IDCartella . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $sottoCartelle = $stmt;

        //Cancella tutte le sottocartelle, le FAD contenute all'interno delle sottocartelle e tutti i dati rendicontanti dagli studenti per ognuna:
        while ($sottoCartella = $sottoCartelle->fetch(PDO::FETCH_OBJ)) {
            //Cancella la sottocartella dal database:
            $query = "DELETE FROM Cartelle WHERE ID = '" . $sottoCartella->ID . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();

            //Prendi ora tutte le FAD contenute all'interno di questa sottocartella:
            $query = "SELECT * FROM FAD WHERE IDCartella = '" . $sottoCartella->ID . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $FADsSottoCartella = $stmt;

            //Cancella tutte le FAD contenute all'interno di questa sottocartella e tutti i dati rendicontati dagli studenti per ognuna:
            while ($FADSottoCartella = $FADsSottoCartella->fetch(PDO::FETCH_OBJ)) {
                //Cancella la FAD dal database:
                $query = "DELETE FROM FAD WHERE ID = '" . $FADSottoCartella->ID . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();

                //Cancella ora i dati rendicontati dagli studenti per questa FAD:
                $query = "DELETE FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE IDFAD = '" . $FADSottoCartella->ID . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
            }
        }

        //Ferma tutto il processo e torna alla pagina precedente:
        $db = null;
        $stmt = null;
        header("Location: ../leTueFAD.php");
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../index.php");
}