<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    require_once "includes/navBar.inc.php";
    if ($_SESSION["ruolo"] !== "studente") {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Area FAD</title>
        <link rel="stylesheet" href="includes/main.css">
        <script src="includes/main.js"></script>
    </head>
    <body>
        <h1>Panoramica Ore svolte/Da svolgere</h1>

        <!--Mostra tutte le materie associate alla tua classe-->
        <?php
            //Prendi l'email dello studente, la classe e l'anno scolastico:
            $email = $_SESSION["email"];
            $classe = implode($_SESSION["classe"]);
            $annoScolastico = implode($_SESSION["annoScolastico"]);

            //Prova a prendere tutte le materie associate alla tua classe:
            try {
                //Connettiti al database:
                require_once "includes/connectDatabase.inc.php";

                //Prendi tutte le materie associate alla tua classe:
                $query = "SELECT * FROM Materie WHERE Classe = '" . $classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $materie = $stmt;

                //Crea il mainDiv all'interno del quale saranno presenti tutte le materie:
                echo '<div class="mainPanoramicaStudenti">';

                //Mostra ogni materia in un div con i rispettivi dati:
                while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                    //Prendi le ore rendicontate dallo studente per questa materia:
                    $query = "SELECT SUM(Minuti) FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE Materia = '" . $materia->Materia . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $oreRendicontate = $stmt->fetch();

                    //Crea il div ed inserisci al suo interno i dati:
                    echo '<div class="panoramicaStudenti">';
                    echo '<h2 class="panoramicaStudenti">' . $materia->Materia . '</h2>';
                    echo '<p class="panoramicaStudenti">(<i>' . $materia->Email_docente . '</i>)</p>';
                    echo '<h3 class="panoramicaStudenti"><strong>Ore rendicontate:</strong></h3>';
                    echo '<p class="panoramicaStudenti"><strong>' . implode($oreRendicontate) . '</strong>/<strong>' . $materia->Monteore_minuti . '</strong></p>';
                    echo '</div>';
                }

                //Chiudi il mainDiv:
                echo '</div>';
            } catch(PDOException $errore) {
                //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                die("Il ritorno dei dati dal database è fallito: " . $errore);
            }
        ?>
    </body>
</html>