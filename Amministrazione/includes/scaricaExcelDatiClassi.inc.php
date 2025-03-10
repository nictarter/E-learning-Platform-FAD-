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
    //Prendi l'anno scolastico in corso:
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova a prendere tutte le classi con i relativi dati e ad inserirli in un file excel:
    try {
        //Connettiti al database:
        include_once "../../includes/connectDatabase.inc.php";

        //Prendi tutte le classi attualmente attive per l'anno scolastico in corso:
        $query = "SELECT * FROM Classi WHERE Anno_scolastico = '" . $annoScolastico . "';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $classi = $stmt;

        //Prendi ora il pacchetto per i file excel:
        include_once "xlsxwriter.class.inc.php";

        //Prepara l'intestazione standard da dare a tutti i file excel indipendentemente dai dati delle classi:
        $header = array(
            "Classe" => "string",
            "Materia" => "string",
            "Email docente" => "string",
            "Monteore annuale (in minuti)" => "integer"
        );

        //Inizializza il file excel da creare:
        $writer = new XLSXWriter();
        $writer->setAuthor("(C) Nicolò Tarter - Area FAD");

        //Prepara i dati da inserire nel file excel per ogni classe:
        while ($classe = $classi->fetch(PDO::FETCH_OBJ)) {
            //Prendi tutti i dati della classe:
            $query = "SELECT * FROM Materie WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $datiClassi = $stmt;

            //Inserisci l'intestazione standard per tutte le classi:
            $writer->writeSheetHeader($annoScolastico, $header, $col_options = ['font-style' => 'bold', 'halign' => 'center', 'valign' => 'center', 'fill' => '#FBE2D5', 'border' => 'left,right,top,bottom']);

            //Prepara l'array che conterrà tutti i dati della classe:
            $data = array();

            //Inserisci quindi tutti i dati della classe:
            while ($datiClasse = $datiClassi->fetch(PDO::FETCH_OBJ)) {
                //Inserisci il dato nell'array:
                array_push($data, array($classe->Classe, $datiClasse->Materia, $datiClasse->Email_docente, $datiClasse->Monteore_minuti));
            }

            //Inserisci dunque i dati della classe presenti nell'array all'interno del file excel:
            foreach ($data as $row) {
                $writer->writeSheetRow($annoScolastico, $row, $row_options = ['halign' => 'center', 'valign' => 'center', 'border' => 'left,right,top,bottom']);
            }
        }

        //Inserisci dunque tutti i dati nel file excel e salvalo:
        $nomeFile = "Dati Classi A.S. " . $annoScolastico . ".xlsx";
        $writer->writeToFile($nomeFile);

        //Prepara il file excel per il download e scaricalo sul PC:
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($nomeFile) . '"');
        header('Content-Length: ' . filesize($nomeFile));
        readfile($nomeFile);

        //Cancellalo dal server non essendo più necessario:
        unlink($nomeFile);
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage:
    header("Location: ../../../index.php");
}