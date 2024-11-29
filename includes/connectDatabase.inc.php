<?php
    //Dati del mio database:
    $dataSourceName ="mysql:host=fdb1030.awardspace.net; dbname=4551228_fad";
    $databaseUsername ="4551228_fad";
    $databasePassword ="passworddatabasefad123";

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