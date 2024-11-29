<?php
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
        <h1>Benvenuto nell'area FAD!</h1>
        <?php
            //Crea la tabella dove mostrare tutte le FAD in scadenza se sei uno studente:
            if ($_SESSION["ruolo"] === "studente") {
                echo '<h3 class="homePageFAD">FAD in scadenza:</h3>';
                echo '<table class="homePageFAD">';
                echo '<tr class="homePageFAD">';
                echo '<th class="homePageFAD">Nome</th>';
                echo '<th class="homePageFAD" style="width:200px">Materia</th>';
                echo '<th class="homePageFAD" style="width:80px">Ore</th>';
                echo '<th class="homePageFAD" style="width:50px">Scadenza</th>';
                echo '<th class="homePageFAD" style="width:30px">Utilità</th>';
                echo '</tr>';
                //Prendi la tua classe e l'anno scolastico in corso:
                $classe = implode($_SESSION["classe"]);
                $annoScolastico = implode($_SESSION["annoScolastico"]);

                //Prova a mostrare tutte le FAD in scadenza:
                try {
                    //Connettiti al database:
                    include_once "includes/connectDatabase.inc.php";

                    //Prendi tutte le FAD in scadenza associate alla tua classe:
                    $query = "SELECT * FROM FAD WHERE Anno_scolastico = '" . $annoScolastico . "' AND Classe = '" . $classe . "' AND Inizio_visibilita <= '" . date('Y-m-d') . "' AND Fine_conteggio >= '" . date('Y-m-d') . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $FAD = $stmt;

                    //Mostra ora tutte le FAD in scadenza associate alla tua classe nella tabella:
                    while ($dettagliFAD = $FAD->fetch(PDO::FETCH_OBJ)) {
                        //Prendi il monteore effettuato dallo studente per questa FAD:
                        $query = "SELECT Minuti FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE IDFAD = " . $dettagliFAD->ID . ";";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $monteoreEffettuatoStudente = $stmt->fetch();

                        //Mostra quindi i dati nella tabella:
                        echo '<tr class="homePageFAD">';
                        echo '<td class="homePageFAD nome">' . $dettagliFAD->Nome . '</td>';
                        echo '<td class="homePageFAD">' . $dettagliFAD->Materia . '</td>';
                        echo '<td class="homePageFAD">' . $monteoreEffettuatoStudente[0] . '/' . $dettagliFAD->Minuti . '</td>';
                        echo '<td class="homePageFAD">' . date("d-m-y", strtotime($dettagliFAD->Fine_conteggio)) . '</td>';
                        echo '<td class="homePageFAD">';

                        //Crea il form nascosto per permettere il caricamento dei dettagli della FAD nell'apposita pagina:
                        echo '<form action="dettagliFAD.php" method="post">';
                        echo '<input type="hidden" name="IDCartella" value="' . $dettagliFAD->IDCartella . '" required>';
                        echo '<button class="homePageFAD"><strong>APRI</strong></button>';
                        echo '</form>';
                    }
                } catch(PDOException $errore) {
                    //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                    die("Il ritorno dei dati dal database è fallito: " . $errore);
                }
            }
            ?>
        </table>
    </body>
</html>