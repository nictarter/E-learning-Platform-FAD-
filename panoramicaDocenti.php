<?php
    require_once "includes/navBar.inc.php";
    if ($_SESSION["ruolo"] !== "docente") {
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
        <h1>Panoramica Studenti</h1>

        <!--Mostra tutte le materie a cui sei attualmente associato nel corrente anno scolastico-->
        <?php
            //Prendi l'email del docente e l'anno scolastico:
            $email = $_SESSION["email"];
            $annoScolastico = implode($_SESSION["annoScolastico"]);

            //Prova a prendere tutte le materie a cui sei stato associato dal database:
            try {
                //Connettiti al database:
                require_once "includes/connectDatabase.inc.php";

                //Prendi tutte le materie a cui sei attualmente associato nel corrente anno scolastico:
                $query = "SELECT * FROM Materie WHERE Anno_scolastico = '" . $annoScolastico . "' AND Email_docente = '" . $email . "';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $materie = $stmt;

                //Mostra ogni classe nel div con i rispettivi dati (sia in generale riguardanti il monteore totale assegnato, sia riguardante il monteore svolto da ogni singolo studente):
                while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                    //Crea il mainDiv
                    echo '<div class="mainPanoramicaDocenti" id="' . $materia->Materia . '-' . $materia->Classe . '" onclick=\'mostraDiv("' . $materia->Materia . '-' . $materia->Classe . '", "info' . $materia->Materia . '-' . $materia->Classe . '")\'>';
                    echo '<p><strong>' . $materia->Materia . ' (' . $materia->Classe . ')</strong></p>';
                    echo '</div>';

                    //Prendi la somma dei minuti di FAD assegnati fino ad ora dal database:
                    $query = "SELECT SUM(Minuti) FROM FAD WHERE Materia = '" . $materia->Materia . "' AND Classe = '" . $materia->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $sommaMinutiFAD = $stmt->fetch();

                    //Inizia il div all'interno del quale si visualizza la panoramica del monteore di una determinata materia:
                    echo '<div class="panoramicaDocenti" id="info' . $materia->Materia . '-' . $materia->Classe . '" style="display: none">';
                    echo '<h3>Monteore totale:</h3>';
                    echo '<h3>' . $sommaMinutiFAD[0] . '/' . $materia->Monteore_minuti . '</h3>';
                    echo '<p><strong>ELENCO STUDENTI:</strong></p>';

                    //Inizia la lista degli studenti:
                    echo '<ul class="panoramicaDocenti">';

                    //Prendi tutti gli studenti associati a questa materia:
                    $query = "SELECT * FROM Studenti WHERE Classe = '" . $materia->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $studenti = $stmt;

                    //Mostra ora tutti gli studenti associati alla materia con il rispettivo monteore svolto come punti della lista disordinata (unordered list):
                    while ($studente = $studenti->fetch(PDO::FETCH_OBJ)) {
                        //Prendi tutte le ore svolte dallo studente in questa materia dal database:
                        $query = "SELECT SUM(Minuti) FROM RendicontoOre_" . preg_replace("/-/", "_", $annoScolastico) . " WHERE Email_studente = ' . $studente->Email . ' AND Materia = ' . $materia->Materia . ';";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $sommaMinutiStudenteFAD = $stmt->fetch();

                        //Mostra ora lo studente come punto della lista disordinata (unordered list) con il rispettivo monteore svolto:
                        echo '<li class="gestisciClassi">' . $studente->Email . ' (' . $sommaMinutiStudenteFAD[0] . '/' . $sommaMinutiFAD[0] . ')</li>';
                    }

                    //Termina la lista ed il div della materia:
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