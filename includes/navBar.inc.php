<?php
    session_start();
    if (isset($_SESSION["ruolo"]) === False and isset($_SESSION["ruoloAdmin"]) === False) {
        header("Location: ../login.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    </head>
    <body>
        <div class="navBar">
            <ul class="navBar">
                <li class="navBar"><a class="navBar" href="index.php">Home</a></li>
                <?php
                    if ($_SESSION["ruolo"] !== "admin") {
                        echo '<li class="navBar"><a class="navBar" href="leTueFAD.php">Le tue FAD</a></li>';
                    }
                    if ($_SESSION["ruolo"] === "docente") {
                        echo '<li class="navBar"><a class="navBar" href="aggiungiFAD.php">Aggiungi una FAD</a></li>';
                    }
                    if ($_SESSION["ruolo"] === "studente") {
                        echo '<li class="navBar"><a class="navBar" href="panoramicaStudenti.php">Panoramica Monteore</a></li>';
                    }
                    if ($_SESSION["ruolo"] === "docente") {
                        echo '<li class="navBar"><a class="navBar" href="panoramicaDocenti.php">Panoramica Docenti</a></li>';
                    }
                    if (isset($_SESSION["ruoloAdmin"]) === True) {
                        echo '<li class="navBar" id="vaiAreaAmministrazione"><a class="navBar" href="Amministrazione/index.php">Amministrazione</a></li>';
                    }
                ?>
                <li class="navBar"><a class="navBar" href="includes/logout.inc.php">Logout</a></li>
            </ul>
        </div>
    </body>
</html>