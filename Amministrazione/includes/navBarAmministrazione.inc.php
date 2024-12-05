<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    session_start();
    if (isset($_SESSION["ruoloAdmin"]) === False) {
        header("Location: ../login.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    </head>
    <body>
        <div class="navBarAmministrazione">
            <ul class="navBarAmministrazione">
                <li class="navBarAmministrazione" id="tornaAreaFAD"><a class="navBarAmministrazione" href="../index.php">Torna all'area FAD</a></li>
                <li class="navBarAmministrazione"><a class="navBarAmministrazione" href="index.php">Home</a></li>
                <li class="navBarAmministrazione"><a class="navBarAmministrazione" href="gestisciClassi.php">Classi</a></li>
                <li class="navBarAmministrazione"><a class="navBarAmministrazione" href="gestisciStudenti.php">Studenti</a></li>
                <li class="navBarAmministrazione"><a class="navBarAmministrazione" href="annoScolastico.php">Anno scolastico</a></li>
            </ul>
        </div>
    </body>
</html>