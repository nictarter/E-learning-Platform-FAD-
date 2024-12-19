<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

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
        <h1>Aggiungi una FAD</h1>
        <div class="divForm">
            <form action="includes/aggiungiFAD.inc.php" method="post" enctype="multipart/form-data">
                <div class="divInput">
                    <p class="labelInput"><strong>Nome del file:</strong></p>
                    <input type="text" name="nome" placeholder="Nome del file" required>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Carica il file:</strong></p>
                    <input type="file" name="file" accept="application/pdf, video/*" required>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Cartella/sottocartella:</strong></p>
                    <select name="cartellaClasseMateria" required>
                        <?php
                            //Prendi l'email del docente e l'anno scolastico in corso:
                            $email = $_SESSION["email"];
                            $annoScolastico = implode($_SESSION["annoScolastico"]);

                            //Prova a mostrare tutte le cartelle/sottocartelle disponibili per le tue classi e materie:
                            try {
                                //Connettiti al database:
                                require_once "includes/connectDatabase.inc.php";

                                //Prendi tutte le materie a cui sei associato (di conseguenza prende anche tutte le classi a cui sei associato):
                                $query = "SELECT * FROM Materie WHERE Anno_scolastico = '" . $annoScolastico . "' AND Email_docente = '" . $email . "' ORDER BY Classe;";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $materie = $stmt;

                                //Per ogni materia, mostra tutte le cartelle/sottocartelle disponibili:
                                while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                                    //Prendi tutte le cartelle principali disponibili per questa materia:
                                    $query = "SELECT * FROM Cartelle WHERE Materia = '" . $materia->Materia . "' AND Classe = '" . $materia->Classe . "' AND Tipo = 'cartella';";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    $cartellePrincipali = $stmt;

                                    //Per ogni cartella principale, mostrala nelle opzioni e mostra anche le sue eventuali sottocartelle:
                                    while ($cartellaPrincipale = $cartellePrincipali->fetch(PDO::FETCH_OBJ)) {
                                        //Mostra la cartella principale come opzione:
                                        echo '<option value="' . $cartellaPrincipale->ID . '-' . $cartellaPrincipale->Classe . '-' . $cartellaPrincipale->Materia . '">' . $cartellaPrincipale->Nome . ' (' . $cartellaPrincipale->Materia . ' - ' . $cartellaPrincipale->Classe . ')</option>';
                                        
                                        //Cerca ora le eventuali sottocartelle di questa cartella:
                                        $query = "SELECT * FROM Cartelle WHERE Materia = '" . $materia->Materia . "' AND Classe = '" . $materia->Classe . "' AND ID_Cartella_Principale = '" . $cartellaPrincipale->ID . "';";
                                        $stmt = $db->prepare($query);
                                        $stmt->execute();
                                        $sottocartelle = $stmt;

                                        //Mostra quindi le sottocartelle di questa cartella:
                                        while ($sottocartella = $sottocartelle->fetch(PDO::FETCH_OBJ)) {
                                            //Mostra la sottocartella come opzione:
                                            echo '<option value="' . $sottocartella->ID . '-' . $sottocartella->Classe . '-' . $sottocartella->Materia . '">--' . $sottocartella->Nome . ' (' . $sottocartella->Materia . ' - ' . $sottocartella->Classe . ')</option>';
                                        }
                                    }
                                }
                            } catch(PDOException $errore) {
                                //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
                                die("L'invio dei dati al database è fallito: " . $errore);
                            }
                        ?>
                    </select>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Descrizione/istruzioni:</strong></p>
                    <textarea name="descrizione" rows="5" placeholder="Inserisci qui le istruzioni da dare agli studenti"></textarea>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Minuti da conteggiare:</strong></p>
                    <input type="number" name="daConteggiare" min="1" placeholder="Minuti da conteggiare" required>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Finestra esclusiva:</strong></p>
                    <select name="finestraEsclusiva" required>
                        <option value="1">Abilitata</option>
                        <option value="0">Disabilitata</option>
                    </select>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Inizio visibilità:</strong></p>
                    <input type="date" name="inizioVisibilita" required>
                </div>
                <div class="divInput">
                    <p class="labelInput"><strong>Termine del conteggio delle ore:</strong></p>
                    <input type="date" name="termineConteggio" required>
                </div>
                <div class="divInvioForm">
                    <button><strong>Carica la FAD</strong></button>
                </div>
            </form>
        </div>
    </body>
</html>