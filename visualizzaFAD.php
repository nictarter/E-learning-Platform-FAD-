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

    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Area FAD</title>
        <link rel="stylesheet" href="includes/main.css">
        <script src="includes/main.js"></script>
    </head>
    <body>
        <div id="navBar+mostraFAD">
            <div class="navBarFAD" id="divNavBarFAD" style="background-color: rgb(196, 61, 61)">
                <button class="navBarFAD" style="display:inline-block" id="home"><a href="index.php"><strong>HOME</strong></a></button>
                <p class="navBarFAD">Tempo accreditato: </p>
                    <p class="navBarFAD"><strong id="ore">00:00</strong></p>
                    <button class="navBarFAD" style="display:inline-block" id="iniziaConteggio" onclick="iniziaConteggioFAD()"><strong>INIZIA IL CONTEGGIO</strong></button>
                    <button class="navBarFAD" style="display:none" id="salvataggioConteggio"><strong>SALVATAGGIO IN CORSO...</strong></button>
                    <form class="navBarFAD" id="formConteggioDatabase" action="includes/salvataggioConteggio.inc.php" method="post">
                        <input type="hidden" value="0" id="conteggioDatabase" name="tempoDaConteggiare">
                        <?php
                            echo '<input type="hidden" name="tipoVisualizzazione" value="' . $_POST["tipoVisualizzazione"] . '" required>';
                            echo '<input type="hidden" name="IDFAD" value="' . $_POST["IDFAD"] . '" required>';
                            echo '<input type="hidden" name="materia" value="' . $_POST["materia"] . '" required>';
                            echo '<input type="hidden" name="classe" value="' . $_POST["classe"] . '" required>';
                            echo '<input type="hidden" name="estensioneFile" value="' . $_POST["estensioneFile"] . '" required>';
                        ?>
                        <button class="navBarFAD" style="display:none" id="terminaConteggio" onclick="rimuoviSchermoIntero()"><strong id="pulsanteConteggio">TERMINA IL CONTEGGIO</strong></button>
                    </form>
            </div>
            <div class="mostraFAD">
                <?php
                    //In base se è un video o un pdf, la FAD viene mostrata tramite il tag <embed> o <video>:
                    if ($_POST["estensioneFile"] === "pdf") {
                        //Se è un pdf:
                        //In base al dispositivo (mobile o pc - viene selezionato dall'utente prima di arrivare qua), mostra il file "nativamente" attraverso il browser o con l'aiuto di Google:
                        echo '<embed class="mostraFAD" id="embedMostraFAD" src="' . (($_POST["tipoVisualizzazione"] === "mobile")? "https://drive.google.com/viewerng/viewer?embedded=true&url=" : "") . 'https://fad.scienceontheweb.net/FAD/' . implode($_SESSION["annoScolastico"]) . '/' . $_POST["classe"] . '/' . $_POST["materia"] . "/" . $_POST["IDFAD"] . "." . $_POST["estensioneFile"] . '">';
                    } else {
                        //Se è un video:
                        echo '<video class="mostraFAD" id="videoMostraFAD" src="FAD/' . implode($_SESSION["annoScolastico"]) . '/' . $_POST["classe"] . '/' . $_POST["materia"] . '/' . $_POST["IDFAD"] . '.' . $_POST["estensioneFile"] . '" controls>';
                    }
                ?>
            </div>
        </div>
    </body>
</html>