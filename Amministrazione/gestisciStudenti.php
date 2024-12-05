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

                //Mostra ogni classe nel div con i rispettivi dati e pulsanti:
                while ($classe = $classi->fetch(PDO::FETCH_OBJ)) {
                    //Crea il mainDiv:
                    echo '<div class="mainGestisciStudenti" id="' . $classe->Classe . '" onclick=\'mostraDiv("' . $classe->Classe . '", "info' . $classe->Classe . '")\'>';
                    echo '<p><strong>' . $classe->Classe . '</strong></p>';
                    echo '</div>';

                    //Inizia il div all'interno del quale si gestiscono gli studenti della classe:
                    echo '<div class="gestisciStudenti" id="info' . $classe->Classe . '" style="display: none">';
                    echo '<p><strong>ELENCO STUDENTI:</strong></p>';

                    //Inizia la lista degli studenti:
                    echo '<ul class="gestisciStudenti">';

                    //Prendi le materie associate a questa classe:
                    $query = "SELECT * FROM Studenti WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $studenti = $stmt;

                    //Mostra tutti gli studenti associati a questa classe come punti della lista disordinata (unordered list):
                    while ($studente = $studenti->fetch(PDO::FETCH_OBJ)) {
                        echo '<li class="gestisciStudenti">' . $studente->Email . '<button onclick=\'rimuoviStudente("' . $studente->ID . '")\' class="rimuoviStudente"><span class="material-icons">delete</span></button></li>';
                        //Form nascosto che permette il funzionamento della rimozione dello studente tramite il pulsante affianco a ogni studente:
                        echo '<form id="rimuovi' . $studente->ID . '" action="includes/rimuoviStudente.inc.php" method="post">';
                        echo '<input type="hidden" name="IDStudente" value="' . $studente->ID . '">';
                        echo '</form>';
                    }

                    //Termina la lista, metti il pulsante per aggiungere studenti (con il relativo form - nascosto) e chiudi il div della gestione degli studenti:
                    echo '<div class="divPulsanteGestisciStudenti">';
                    echo '<form action="includes/aggiungiStudenti.inc.php" id="form' . $classe->Classe . '" method="post">';
                    echo '<input type="hidden" id="email' . $classe->Classe . '" name="email" value="" required>';
                    echo '<input type="hidden" id="' . $classe->Classe . '" name="classe" value="' . $classe->Classe . '" required>';
                    echo '<button class="gestisciStudenti" type="button" onclick=\'aggiungiStudenti("' . $classe->Classe . '")\'><strong>AGGIUNGI STUDENTI</strong></button>';
                    echo '</form>';
                    echo '</div>';
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