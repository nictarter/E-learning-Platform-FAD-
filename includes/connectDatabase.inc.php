<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    //Dati del mio database:
    $dataSourceName ="***YOURDATA***; dbname=***YOURDATA***";
    $databaseUsername ="***YOURDATA***";
    $databasePassword ="***YOURDATA***";

    //Prova di connessione al database:
    try {
        //Connettiti al database:
        $db = new PDO($dataSourceName, $databaseUsername, $databasePassword);
        //Controlla se c'è un errore:
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //Se c'è un errore dillo:
    } catch(PDOException $errore) {
        echo 'Connessione fallita: ' . $errore->getMessage();
    }