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
        <h1>Gestione Studenti</h1>
        
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

                //Prendi il numero di studenti previsti in totale per l'anno scolastico in corso:
                $query = "SELECT SUM(Numero_studenti) FROM Classi WHERE Anno_scolastico = '" . $annoScolastico . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $totaleStudentiPrevisti = $stmt->fetch();

                //Prendi il numero degli studenti i cui dati sono presenti nel database per l'anno scolastico in corso:
                $query = "SELECT COUNT(Email) FROM Studenti WHERE Anno_scolastico = '" . $annoScolastico . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $totaleStudentiEffettivi = $stmt->fetch();

                if ($totaleStudentiPrevisti[0] > $totaleStudentiEffettivi[0]) {
                    echo '<div class="divPulsanteGestisciStudenti">';
                    echo '<form action="includes/scaricaExcelDatiStudenti.inc.php" method="post">';
                    echo '<button class="gestisciStudenti"><strong>SCARICA FILE EXCEL</strong></button>';
                    echo '</form>';
                    echo '</div>';

                    echo '<div class="divPulsanteGestisciStudenti">';
                    echo '<form id="formCaricaCSVDatiStudenti" action="includes/caricaCSVDatiStudenti.inc.php" method="post" enctype="multipart/form-data">';
                    echo '<input type="file" name="datiStudentiCSV" id="datiStudentiCSV" style="display: none;" accept=".csv" required>';
                    echo '<button type="submit" class="gestisciStudenti" style="margin-right: 5px;"><strong>CARICA FILE CSV</strong></button>';
                    echo '<button type="button" class="gestisciStudenti" onclick="caricaCSVDatiStudenti()"><span class="material-icons">upload_file</span></button>';
                    echo '</form>';
                    echo '</div>';
                }

                //Mostra ogni classe nel div con i rispettivi dati e pulsanti:
                while ($classe = $classi->fetch(PDO::FETCH_OBJ)) {
                    //Crea il mainDiv:
                    echo '<div class="mainGestisciStudenti" id="' . $classe->Classe . '" onclick=\'mostraDiv("' . $classe->Classe . '", "info' . $classe->Classe . '")\'>';
                    echo '<p><strong>' . $classe->Classe . '</strong></p>';
                    echo '</div>';

                    //Inizia il div all'interno del quale si gestiscono gli studenti della classe:
                    echo '<div class="gestisciStudenti" id="info' . $classe->Classe . '" style="display: none">';

                    //Prendi il numero di studenti previsti per questa classe:
                    $query = "SELECT * FROM Classi WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $numeroStudentiPrevisti = $stmt->fetch(PDO::FETCH_OBJ);

                    //Prendi il numero di studenti effettivamente associati a questa classe (dunque i cui dati risultano effettivamente inseriti nel database):
                    $query = "SELECT COUNT(Email) FROM Studenti WHERE Email != '' AND Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $numeroStudentiEffettivi = $stmt->fetch();

                    //Mostra il numero di studenti previsti per questa classe:
                    echo '<p style="padding-bottom: 5px;"><strong>NUMERO DI STUDENTI PREVISTI: ' . $numeroStudentiPrevisti->Numero_studenti . '</strong><button class="gestisciStudenti funzionePiccolo" onclick=\'modificaNumeroStudentiPrevisti("' . $classe->Classe . '", "' . $numeroStudentiEffettivi[0] . '")\'><span class="material-icons">edit</span></button></p>';

                    //Mostra ora il numero di studenti effettivi (dunque con i dati effettivamente inseriti nel database) di questa classe:
                    echo '<p style="padding-bottom: 5px;' . (($numeroStudentiPrevisti->Numero_studenti !== $numeroStudentiEffettivi[0])? "color: red;" : "") . '"><strong>NUMERO DI STUDENTI EFFETTIVAMENTE REGISTRATI: ' . $numeroStudentiEffettivi[0] . '</strong>' . (($numeroStudentiPrevisti->Numero_studenti > $numeroStudentiEffettivi[0])? "<button class=\"gestisciStudenti funzionePiccolo\" onclick=aggiungiStudenti(\"$classe->Classe\")><span class=\"material-icons\">person_add</span></button>" : "") . '</p>';

                    //Mostra ora gli studenti associati a questa classe:
                    echo '<p><strong>ELENCO STUDENTI:</strong></p>';

                    //Prendi gli studenti associati a questa classe:
                    $query = "SELECT * FROM Studenti WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $studenti = $stmt;

                    //Prendi gli ID degli studenti previsti ma non ancora associati a questa classe:
                    $query = "SELECT * FROM Studenti WHERE Email = '' AND Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $studentiNonAncoraAssociati = $stmt;

                    //Crea gli array che conterranno tutti i dati degli studenti:
                    $arrayIDStudenti = array();
                    $arrayEmailStudenti = array();
                    $arrayStudentiNonAncoraAssociati = array();

                    //Mostra tutti gli studenti associati a questa classe come punti della lista disordinata (unordered list):
                    while ($studente = $studenti->fetch(PDO::FETCH_OBJ)) {
                        array_push($arrayIDStudenti, $studente->ID);
                        array_push($arrayEmailStudenti, $studente->Email);
                    }

                    while ($studenteNonAncoraAssociato = $studentiNonAncoraAssociati->fetch(PDO::FETCH_OBJ)) {
                        array_push($arrayStudentiNonAncoraAssociati, $studenteNonAncoraAssociato->ID);
                    }

                    //Controlla se esistono studenti associati alla classe:
                    if (count($arrayIDStudenti) > 0 and (count($arrayStudentiNonAncoraAssociati) !== count($arrayIDStudenti) or count($arrayStudentiNonAncoraAssociati) === 0)) {
                        //Se degli studenti sono associati alla classe, inizia la lista degli studenti:
                        echo '<ul class="gestisciStudenti">';

                        //Inserisci nella lista tutti gli studenti:
                        for ($i = 0; $i < count($arrayIDStudenti); $i++) {
                            if ($arrayEmailStudenti[$i] !== "") {
                            echo '<li class="gestisciStudenti">' . $arrayEmailStudenti[$i] . '<button onclick=\'rimuoviStudente("' . $arrayIDStudenti[$i] . '")\' class="rimuoviStudente"><span class="material-icons">delete</span></button></li>';
                            //Form nascosto che permette il funzionamento della rimozione dello studente tramite il pulsante affianco a ogni studente:
                            echo '<form id="rimuovi' . $arrayIDStudenti[$i] . '" action="includes/rimuoviStudente.inc.php" method="post">';
                            echo '<input type="hidden" name="IDStudente" value="' . $arrayIDStudenti[$i] . '">';
                            echo '</form>';
                            }
                        }

                        //Termina la lista degli studenti:
                        echo '</ul>';
                    } else {
                        //Se nessuno studente è associato alla classe, dillo:
                        echo '<p class="gestisciStudenti">Nessuno studente risulta associato alla classe.</p>';
                    }

                    //Metti il pulsante per aggiungere studenti (con il relativo form - nascosto) e chiudi il div della gestione degli studenti:
                    echo '<div class="divPulsanteGestisciStudenti">';
                    echo '<form action="includes/aggiungiStudenti.inc.php" id="form' . $classe->Classe . '" method="post">';
                    echo '<input type="hidden" id="email' . $classe->Classe . '" name="email" value="" required>';
                    echo '<input type="hidden" id="' . $classe->Classe . '" name="classe" value="' . $classe->Classe . '" required>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
                //Form nascosto per permettere il cambio del numero di studenti previsti per ogni classe (in ogni div della classe c'è un pulsante per abilitare fare ciò):
                echo '<form action="includes/modificaNumeroStudentiPrevisti.inc.php" id="modificaNumeroStudentiPrevisti" method="post">';
                echo '<input type="hidden" id="classeModificaNumeroStudentiPrevisti" name="classe" value="" required>';
                echo '<input type="hidden" id="numeroStudentiPrevistiDesiderato" name="numeroStudentiPrevistiDesiderato" value="" required>';
                echo '</form>';
            } catch(PDOException $errore) {
                //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                die("Il ritorno dei dati dal database è fallito: " . $errore);
            }
        ?>
    </body>
</html>