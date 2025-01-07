<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    require_once "includes/navBarAmministrazione.inc.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Area FAD</title>
        <link rel="stylesheet" href="../includes/main.css">
        <script src="../includes/main.js"></script>
    </head>
    <body>
        <h1>Gestione Classi</h1>

        <!-- Mostra tutte le classi attualmente associate al corrente anno scolastico:-->
        <?php
            //Prendi l'anno scolastico in corso:
            $annoScolastico = implode($_SESSION["annoScolastico"]);

            //Prova a prendere tutte le classi attualmente associate al corrente anno scolastico dal database:
            try {
                //Connettiti al database:
                include_once "../includes/connectDatabase.inc.php";

                //Prendi tutte le classi attualmente esistenti nel corrente anno scolastico:
                $query = "SELECT * FROM Classi WHERE Anno_scolastico = '" . $annoScolastico . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $classi = $stmt;

                //Pulsante per aggiungere classi:
                echo '<div class="divPulsanteGestisciClassi">';
                echo '<button class="gestisciClassi" onclick="aggiungiClasse()"><strong>AGGIUNGI CLASSE</strong></button>';
                echo '</div>';

                //Controlla se a tutte le materie dell'anno scolastico in corso sono stati assegnati docente e monteore annuale:
                $query = "SELECT * FROM Materie WHERE Anno_scolastico = '" . $annoScolastico . "' AND Email_docente IS NULL OR Monteore_minuti IS NULL;";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $assegnazioni = $stmt;

                //Permetti il caricamento dei dati di assegnazione docenti e monteore annuale alle materie tramite file excel se non sono ancora stati assegnati:
                if (!$assegnazioni->fetch(PDO::FETCH_OBJ)) {
                    echo '<div class="divPulsanteGestisciClassi">';
                    echo '<form action="includes/scaricaExcelDatiClassi.inc.php" method="post">';
                    echo '<button class="gestisciClassi" ><strong>SCARICA FILE EXCEL</strong></button>';
                    echo '</form>';
                    echo '</div>';

                    echo '<div class="divPulsanteGestisciClassi">';
                    echo '<form id="formCaricaCSVDatiClassi" action="includes/caricaCSVDatiClassi.inc.php" method="post" enctype="multipart/form-data">';
                    echo '<input type="file" name="datiClassiCSV" id="datiClassiCSV" style="display: none;" accept=".csv" required>';
                    echo '<button type="submit" class="gestisciClassi" style="margin-right: 5px;"><strong>CARICA FILE CSV</strong></button>';
                    echo '<button type="button" class="gestisciClassi" onclick="caricaCSVDatiClassi()"><span class="material-icons">upload_file</span></button>';
                    echo '</form>';
                    echo '</div>';
                }

                //Mostra ogni classe nel div con i rispettivi dati e pulsanti:
                while ($classe = $classi->fetch(PDO::FETCH_OBJ)) {
                    //Crea il mainDiv:
                    echo '<div class="mainGestisciClassi" id="' . $classe->Classe . '" onclick=\'mostraDiv("' . $classe->Classe . '", "info' . $classe->Classe . '")\'>';
                    echo '<p><strong>' . $classe->Classe . '</strong></p>';
                    echo '</div>';

                    //Inizia il div all'interno del quale si gestisce la classe:
                    echo '<div class="gestisciClassi" id="info' . $classe->Classe . '" style="display: none">';
                    echo '<p><strong>ELENCO MATERIE:</strong></p>';

                    //Inizia la lista delle materie:
                    echo '<ul class="gestisciClassi">';

                    //Prendi le materie associate a questa classe:
                    $query = "SELECT * FROM Materie WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $materie = $stmt;

                    //Mostra tutte le materie associate a questa classe come punti della lista disordinata (unordered list):
                    while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                        echo '<li class="gestisciClassi"><strong>' . $materia->Materia . '</strong> (' . (($materia->Email_docente !== "")? $materia->Email_docente : "<i>Da assegnare</i>") . ' - ' . (($materia->Monteore_minuti !== 0)? $materia->Monteore_minuti . " minuti assegnati" : "<i>" . $materia->Monteore_minuti . " minuti assegnati</i>") . ')<button onclick=\'assegnaDocente("' . $materia->ID . '")\' class="assegnaDocente" style="' . ((empty($materia->Email_docente) === True)? "background-color: rgb(247, 173, 77);" : "background-color: rgb(120, 240, 83);") . '"><span class="material-icons">person_add</span></button><button onclick=\'assegnaMonteOreAnnuale("' . $materia->ID . '")\' class="assegnaMonteOreAnnuale" style="' . ((empty($materia->Monteore_minuti) === True)? "background-color: rgb(247, 173, 77);" : "background-color: rgb(120, 240, 83);") . '"><span class="material-icons">timelapse</span></button></li>';
                        //Form nascosto che permette il funzionamento dell'assegnazione del docente alla materia tramite il pulsante affianco a ogni materia:
                        echo '<form id="formAssegnazioneDocente' . $materia->ID . '" action="includes/assegnaDocenteMateria.inc.php" method="post">';
                        echo '<input type="hidden" id="email' . $materia->ID . '" name="email" value="">';
                        echo '<input type="hidden" id="ID' . $materia->ID . '" name="IDMateria" value="' . $materia->ID . '">';
                        echo '</form>';
                        //Form nascosto che permette il funzionamento dell'assegnazione del monteore annuale della materia:
                        echo '<form id="formAssegnazioneMonteOre' . $materia->ID . '" action="includes/assegnaMonteOreAnnualeMateria.inc.php" method="post">';
                        echo '<input type="hidden" id="monteOreAnnuale' . $materia->ID . '" name="monteOreAnnuale" value="">';
                        echo '<input type="hidden" id="ID' . $materia->ID . '" name="IDMateria" value="' . $materia->ID . '">';
                        echo '</form>';
                    }

                    //Termina la lista ed il div della gestione della classe:
                    echo '</ul>';
                    echo '</div>';
                }
            } catch(PDOException $errore) {
                //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                die("Il ritorno dei dati dal database è fallito: " . $errore);
            }
        ?>
    </body>
</html>