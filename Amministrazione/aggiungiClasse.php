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
        <h1>Aggiungi una Classe</h1>
        <div class="divForm">
            <form action="includes/aggiungiClasse.inc.php" method="post">
                <div class="divInput">
                    <p class="labelInput"><strong>Anno scolastico:</strong></p>
                    <?php
                        echo '<input type="text" name="annoScolastico" placeholder="Anno scolastico" value="' . implode($_SESSION["annoScolastico"]) . '" readonly required>';
                    ?>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Classe:</strong></p>
                    <select name="classe" required>
                        <option value="1LOS4">1LOS4</option>
                        <option value="2LOS4">2LOS4</option>
                        <option value="3LOS4">3LOS4</option>
                        <option value="4LOS4">4LOS4</option>
                    </select>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Numero di studenti:</strong></p>
                    <input type="number" name="numeroStudenti" min="1" placeholder="Numero degli studenti" required>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Materia:</strong></p>

                    <!-- Mostra tutte materie che possono essere associate alla classe (checkbox):-->
                    <?php
                        //Prova a prendere tutte le materie associate al corso LOS4 dal database:
                        try {
                            //Connettiti al database:
                            require_once "../includes/connectDatabase.inc.php";

                            //Prendi tutte le materie del corso LOS4:
                            $query = "SELECT * FROM Materie_corso";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $materie = $stmt;

                            //Mostra tutte le opzioni come checkbox:
                            while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                                echo '<div>';
                                echo '<input type="checkbox" class="checkbox" id="' . $materia->Materia_value . '" name="' . $materia->Materia_value . '" value="' . $materia->Materia_value . '">';
                                echo '<label for="' . $materia->Materia_value . '" class="checkbox">' . $materia->Materia . '</label>';
                                echo '</div>';
                            }
                        } catch (PDOException $errore) {
                            //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                            die("Il ritorno dei dati dal database è fallito: " . $errore);
                        }
                    ?>
                </div>
                <div class="divInvioForm">
                    <button><strong>Crea la classe</strong></button>
                </div>
            </form>
        </div>
    </body>
</html>