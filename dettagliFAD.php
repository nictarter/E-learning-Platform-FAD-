<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    //Se non sei arrivato qui come avresti dovuto, torna alla pagina principale:
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
    }
    
    require_once "includes/navBar.inc.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Area FAD</title>
        <link rel="stylesheet" href="includes/main.css">
        <script src="includes/main.js"></script>
    </head>
    <body>
        <h1>Dettagli FAD</h1>

        <!--Mostra tutte le FAD associate alla cartella di cui si sta visualizzando la FAD-->
        <?php
            //Prendi l'ID della cartella:
            $IDCartella = $_POST["IDCartella"];

            //Prova a prendere tutte le FAD all'interno della cartella:
            try {
                //Connettiti al database:
                require_once "includes/connectDatabase.inc.php";

                //Prendi tutte le FAD all'interno della cartella:
                $query = "SELECT * FROM FAD WHERE IDCartella = '" . $IDCartella . "' ORDER BY Fine_conteggio;";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $FADs = $stmt;

                //Mostra ogni FAD nel div con i rispettivi dati:
                while ($FAD = $FADs->fetch(PDO::FETCH_OBJ)) {
                    //Crea il mainDiv:
                    echo '<div class="mainDettagliFAD" id="' . $FAD->ID . '" onclick=\'mostraDiv("' . $FAD->ID . '", "info' . $FAD->ID . '")\'>';
                    echo '<p><strong>' . $FAD->Nome . '</strong></p>';
                    echo '</div>';

                    //Crea il div contenente i dati della FAD:
                    echo '<div class="dettagliFAD" id="info' . $FAD->ID . '" style="display: none">';
                    echo '<p><strong>NOME: </strong>' . $FAD->Nome . '</p>';
                    echo '<p><strong>MATERIA: </strong>' . $FAD->Materia . '</p>';
                    echo '<p><strong>DESCRIZIONE: </strong>' . $FAD->Descrizione . '</p>';
                    echo '<p><strong>MINUTI DA EFFETTUARE: </strong>' . $FAD->Minuti . '</p>';
                    echo '<p><strong>TERMINE CONTEGGIO ORE: </strong>' . $FAD->Fine_conteggio . '</p>';
                    echo '<button onclick="aperturaFAD(\'' . $FAD->ID . '\', \'pc\')">ACCEDI ORA</button>';
                    echo '<button onclick="aperturaFAD(\'' . $FAD->ID . '\', \'mobile\')">Dispositivo mobile</button>';
                    echo '</div>';

                    //Crea il form nascosto per permettere l'apertura della FAD:
                    echo '<form action="visualizzaFAD.php" id="formVisualizzazioneFAD' . $FAD->ID . '" method="post">';
                    echo '<input type="hidden" name="tipoVisualizzazione" id="tipoVisualizzazione' . $FAD->ID . '" value="" required>';
                    echo '<input type="hidden" name="IDFAD" value="' . $FAD->ID . '" required>';
                    echo '<input type="hidden" name="materia" value="' . $FAD->Materia . '" required>';
                    echo '<input type="hidden" name="classe" value="' . $FAD->Classe . '" required>';
                    echo '<input type="hidden" name="estensioneFile" value="' . $FAD->Estensione_file . '" required>';
                    echo '</form>';
                }
            } catch(PDOException $errore) {
                //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                die("Il ritorno dei dati dal database è fallito: " . $errore);
            }
        ?>
    </body>
</html>