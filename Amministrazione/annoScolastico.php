<?php
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
        <h1>Gestione Anno Scolastico</h1>
        <h3>Anno scolastico corrente:</h3>
        <?php
            echo '<h1>' . implode($_SESSION["annoScolastico"]) . '</h1>';
        ?>
        <form action="includes/aggiungiAnnoScolastico.inc.php" id="formCambioAnnoScolastico" method="post">
            <input type="hidden" id="nuovoAnnoScolastico" name="nuovoAnnoScolastico" value="">
        </form>
        <div id="divPulsanteInvioAnnoScolastico">
            <button class="annoScolastico" onclick="nuovoAnnoScolastico()"><strong>Nuovo Anno Scolastico</strong></button>
        </div>
    </body>
</html>