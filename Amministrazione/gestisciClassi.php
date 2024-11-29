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
        <h1>Gestione Classi</h1>
        <div class="divPulsanteGestisciClassi">
                <button class="gestisciClassi" onclick="aggiungiClasse()"><strong>AGGIUNGI CLASSE</strong></button>
        </div>

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
                    echo '<div class="mainGestisciClassi" id="' . $classe->Classe . '" onclick=\'mostraDiv("' . $classe->Classe . '", "info' . $classe->Classe . '")\'>';
                    echo '<p><strong>' . $classe->Classe . '</strong></p>';
                    echo '</div>';

                    //Inizia il div all'interno del quale si gestisce la classe:
                    echo '<div class="gestisciClassi" id="info' . $classe->Classe . '" style="display: none">';
                    echo '<p><strong>ELENCO MATERIE:</strong></p>';

                    //Inizia la lista delle materie:
                    echo '<ul class="gestisciClassi">';

                    //Prendi le materie associate a questa classe:
                    $query = "SELECT * FROM Materie WHERE Classe = '" . $classe->Classe . "' AND Anno_scolastico = '" . $annoScolastico . "';";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $materie = $stmt;

                    //Mostra tutte le materie associate a questa classe come punti della lista disordinata (unordered list):
                    while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                        echo '<li class="gestisciClassi">' . $materia->Materia . '<button onclick=\'assegnaDocente("' . $materia->ID . '")\' class="assegnaDocente" style="' . ((empty($materia->Email_docente) === True)? "background-color: rgb(247, 173, 77);" : "background-color: rgb(120, 240, 83);") . '"><span class="material-icons">person_add</span></button><button onclick=\'assegnaMonteOreAnnuale("' . $materia->ID . '")\' class="assegnaMonteOreAnnuale" style="' . ((empty($materia->Monteore_minuti) === True)? "background-color: rgb(247, 173, 77);" : "background-color: rgb(120, 240, 83);") . '"><span class="material-icons">timelapse</span></button></li>';
                        //Form nascosto che permette il funzionamento dell'assegnazione del docente alla materia tramite il pulsante affianco a ogni materia:
                        echo '<form id="formAssegnazioneDocente' . $materia->ID . '" action="includes/assegnaDocenteMateria.inc.php" method="post">';
                        echo '<input type="hidden" id="email' . $materia->ID . '" name="email" value="">';
                        echo '<input type="hidden" id="ID' . $materia->ID . '" name="IDMateria" value="' . $materia->ID . '">';
                        echo '</form>';
                        //Form nascosto che permette il funzionamento dell'assegnazione del monteore annuale della materia:
                        echo '<form id="formAssegnazioneMonteOre' . $materia->ID . '" action="includes/assegnaMonteOreAnnualeMateria.inc.php" method="post">';
                        echo '<input type="hidden" id="monteOreAnnuale' . $materia->ID . '" name="monteOreAnnuale" value="">';
                        echo '<input type="hidden" id="ID' . $materia->ID . '" name="IDMateria" value="' . $materia->ID . '">';
                        echo '</form>';
                    }

                    //Termina la lista ed il div della gestione della classe:
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