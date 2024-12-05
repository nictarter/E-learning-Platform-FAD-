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
    //Prendi tutti i dati relativi alla cartella da aggiungere e l'anno scolastico in corso:
    $nome = $_POST["nome"];
    $tipoCartella = $_POST["tipoCartella"];
    $cartellaPrincipale = $_POST["cartellaPrincipale"];
    $materia = $_POST["materia"];
    $classe = $_POST["classe"];
    $annoScolastico = implode($_SESSION["annoScolastico"]);

    //Prova ad inserire la cartella nel database:
    try {
        //Connettiti al database:
        require_once "connectDatabase.inc.php";

        //Controlla se è una cartella o sottocartella ed in base a ciò inserisci la cartella nel database:
        if ($tipoCartella === "cartella") {
            //Inserisci la cartella nel database:
            $query = "INSERT INTO Cartelle (Nome, Materia, Classe, Tipo, Anno_scolastico) VALUES (?, ?, ?, ?, ?);";
            $stmt = $db->prepare($query);
            $stmt->execute([$nome, $materia, $classe, $tipoCartella, $annoScolastico]);
        } else {
            //Inserisci la sottocartella nel database:
            $query = "INSERT INTO Cartelle (Nome, Materia, Classe, Tipo, Anno_scolastico, ID_Cartella_Principale) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $db->prepare($query);
            $stmt->execute([$nome, $materia, $classe, $tipoCartella, $annoScolastico, $cartellaPrincipale]);
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