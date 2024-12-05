<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

    require_once "includes/navBar.inc.php";
    if ($_SESSION["ruolo"] === "admin") {
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
        <h1>Le tue FAD</h1>

        <div class="leTueFAD">
            <!--Mostra ogni FAD a cui sei associato (come docente o come studente):-->
            <?php
                //Prendi l'anno scolastico ed il ruolo dell'utente (può essere uno studente oppure un docente) la/le classe/i dell'utente (il numero di utenti dipende dal ruolo: studente o docente):
                $annoScolastico = implode($_SESSION["annoScolastico"]);
                $ruolo = $_SESSION["ruolo"];

                //Prova a connetterti al database (servirà successivamente in base al ruolo dello studente):
                try {
                    //Connettiti al database:
                    require_once "includes/connectDatabase.inc.php";

                    //*******//
                    //Se sei uno studente:
                    //*******//

                    if($ruolo === "studente") {
                        //Prendi la tua classe:
                        $classe = implode($_SESSION["classe"]);

                        //Prendi ora tutte le materie della tua classe:
                        $query = "SELECT * FROM Materie WHERE Anno_scolastico = '" . $annoScolastico . "' AND Classe = '" . $classe . "';";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $materie = $stmt;

                        //Crea ora una cartella per ogni materia, inserendo all'interno della cartella il relativo materiale:
                        while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                            //Crea la cartella della materia:
                            echo '<p class="cartellaMateriaLeTueFAD"><span class="material-icons cartellaMateriaLeTueFAD">topic</span><strong>' . $materia->Materia . '</strong></p>';

                            //Prendi ora tutte le cartelle relative a questa materia:
                            $query = "SELECT * FROM Cartelle WHERE Anno_scolastico = '" . $annoScolastico . "' AND Classe = '" . $classe . "' AND Materia = '" . $materia->Materia . "' AND Tipo = 'cartella';";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $cartellePrincipali = $stmt;

                            //Crea ora tutte le cartelle principali associate a questa materia, inserendo all'interno delle cartelle il relativo materiale:
                            while ($cartellaPrincipale = $cartellePrincipali->fetch(PDO::FETCH_OBJ)) {
                                //Prendi ora tutti i file associati a questa cartella (li prendo prima di creare la cartella perchè mi serve sapere se la cartella contiene file):
                                $query = "SELECT * FROM FAD WHERE IDCartella = '" . $cartellaPrincipale->ID . "';";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $filesCartellaPrincipale = $stmt;

                                //Crea una delle cartelle principali della materia:
                                echo '<p class="cartellaPrincipaleLeTueFAD"><span class="material-icons cartellaPrincipaleLeTueFAD">folder</span><strong>' . $cartellaPrincipale->Nome . '</strong><button onclick=\'apriDettagliCartellaFAD("' . $cartellaPrincipale->ID . '")\' class="funzioniLeTueFAD"><span class="material-icons">not_started</span></button></p>';

                                //Mostra ora tutti i file associati a questa cartella:
                                while ($fileCartellaPrincipale = $filesCartellaPrincipale->fetch(PDO::FETCH_OBJ)) {
                                    //Mostra ora il file associato alla cartella principale della materia:
                                    echo '<p class="fileCartellaPrincipaleLeTueFAD"><span class="material-icons fileCartellaPrincipaleLeTueFAD">description</span><strong>' . $fileCartellaPrincipale->Nome . '</strong></p>';
                                }

                                //Prendi ora tutte le sottocartelle associate a questa cartella:
                                $query = "SELECT * FROM Cartelle WHERE ID_Cartella_Principale = '" . $cartellaPrincipale->ID . "';";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $sottoCartelle = $stmt;

                                //Crea ora tutte le sottocartelle associate a questa cartella, inserendo all'interno delle cartelle il relativo materiale:
                                while ($sottoCartella = $sottoCartelle->fetch(PDO::FETCH_OBJ)) {
                                    //Prendi ora tutti i file associati a questa sottocartella (li prendo prima di creare la cartella perchè mi serve sapere se la cartella contiene file):
                                    $query = "SELECT * FROM FAD WHERE IDCartella = '" . $sottoCartella->ID . "';";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    $filesSottoCartella = $stmt;

                                    //Crea una delle sottocartelle:
                                    echo '<p class="subFolderLeTueFAD"><span class="material-icons subFolderLeTueFAD">folder</span><strong>' . $sottoCartella->Nome . '</strong><button onclick=\'apriDettagliCartellaFAD("' . $sottoCartella->ID . '")\' class="funzioniLeTueFAD"><span class="material-icons">not_started</span></button></p>';

                                    //Mostra ora tutti i file associati a questa sottocartella:
                                    while ($fileSottoCartella = $filesSottoCartella->fetch(PDO::FETCH_OBJ)) {
                                        //Mostra ora il file associato alla sottocartella:
                                        echo '<p class="fileSubFolderLeTueFAD"><span class="material-icons fileSubFolderLeTueFAD">description</span><strong>' . $fileSottoCartella->Nome . '</strong></p>';
                                    }
                                }
                            }
                        }
                        //Form nascosto per permettere l'apertura da parte dello studente dei dettagli della FAD:
                        echo '<form id="apriDettagliCartellaStudente" action="dettagliFAD.php" method="post">';
                        echo '<input type="hidden" id="IDCartella" name="IDCartella" value="" required>';
                        echo '</form>';
                    }

                    //*******//
                    //Se sei un docente, prendi dal database tutte le classi a cui sei associato e, per ognuna, mostrane le FAD della tua materia:
                    //*******//

                    else if($ruolo === "docente") {
                        //Prendi la tua email:
                        $email = $_SESSION["email"];

                        //Prendi ora tutte le tue materie (con classe annessa):
                        $query = "SELECT * FROM Materie WHERE Anno_scolastico = '" . $annoScolastico . "' AND Email_docente = '" . $email . "' ORDER BY Classe;";
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $materie = $stmt;

                        //Crea ora una cartella per ogni materia, inserendo all'interno della cartella il relativo materiale:
                        while ($materia = $materie->fetch(PDO::FETCH_OBJ)) {
                            //Crea la cartella della materia:
                            echo '<p class="cartellaMateriaLeTueFAD"><span class="material-icons cartellaMateriaLeTueFAD">topic</span><strong>' . $materia->Materia . ' (' . $materia->Classe . ')</strong><button onclick=\'aggiungiCartella("cartella", "' . $materia->Materia . '", "' . $materia->Materia . '", "' . $materia->Classe . '")\' class="funzioniLeTueFAD"><span class="material-icons" style="color: black;">create_new_folder</span></button></p>';

                            //Prendi ora tutte le cartelle relative a questa materia:
                            $query = "SELECT * FROM Cartelle WHERE Anno_scolastico = '" . $annoScolastico . "' AND Classe = '" . $materia->Classe . "' AND Materia = '" . $materia->Materia . "' AND Tipo = 'cartella';";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $cartellePrincipali = $stmt;

                            //Crea ora tutte le cartelle principali associate a questa materia, inserendo all'interno delle cartelle il relativo materiale:
                            while ($cartellaPrincipale = $cartellePrincipali->fetch(PDO::FETCH_OBJ)) {
                                //Crea una delle cartelle principali della materia:
                                echo '<p class="cartellaPrincipaleLeTueFAD"><span class="material-icons cartellaPrincipaleLeTueFAD">folder</span><strong>' . $cartellaPrincipale->Nome . '</strong><button onclick=\'aggiungiCartella("sottocartella", "' . $cartellaPrincipale->ID . '", "' . $materia->Materia . '", "' . $materia->Classe . '")\' class="funzioniLeTueFAD"><span class="material-icons">create_new_folder</span></button><button onclick=\'cancellaCartella("' . $cartellaPrincipale->ID . '")\' class="funzioniLeTueFAD" style="background-color: rgb(175, 99, 99)"><span class="material-icons">delete</span></button></p>';

                                //Prendi ora tutti i file associati a questa cartella:
                                $query = "SELECT * FROM FAD WHERE IDCartella = '" . $cartellaPrincipale->ID . "';";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $filesCartellaPrincipale = $stmt;

                                //Mostra ora tutti i file associati a questa cartella:
                                while ($fileCartellaPrincipale = $filesCartellaPrincipale->fetch(PDO::FETCH_OBJ)) {
                                    //Mostra ora il file associato alla cartella principale della materia:
                                    echo '<p class="fileCartellaPrincipaleLeTueFAD"><span class="material-icons fileCartellaPrincipaleLeTueFAD">description</span><strong>' . $fileCartellaPrincipale->Nome . '</strong><button onclick=\'visualizzaAnteprimaDocentiFAD("' . $annoScolastico . '", "' . $materia->Classe . '", "' . $materia->Materia . '", "' . $fileCartellaPrincipale->ID . '", "' . $fileCartellaPrincipale->Estensione_file . '")\' class="funzioniLeTueFAD"><span class="material-icons">not_started</span></button><button onclick=\'cancellaFAD("' . $fileCartellaPrincipale->ID . '")\' class="funzioniLeTueFAD" style="background-color: rgb(175, 99, 99)"><span class="material-icons">delete</span></button></p>';
                                }

                                //Prendi ora tutte le sottocartelle associate a questa cartella:
                                $query = "SELECT * FROM Cartelle WHERE ID_Cartella_Principale = '" . $cartellaPrincipale->ID . "';";
                                $stmt = $db->prepare($query);
                                $stmt->execute();
                                $sottoCartelle = $stmt;

                                //Crea ora tutte le sottocartelle associate a questa cartella, inserendo all'interno delle cartelle il relativo materiale:
                                while ($sottoCartella = $sottoCartelle->fetch(PDO::FETCH_OBJ)) {
                                    //Crea una delle sottocartelle:
                                    echo '<p class="subFolderLeTueFAD"><span class="material-icons subFolderLeTueFAD">folder</span><strong>' . $sottoCartella->Nome . '</strong><button onclick=\'cancellaCartella("' . $sottoCartella->ID . '")\' class="funzioniLeTueFAD" style="background-color: rgb(175, 99, 99)"><span class="material-icons">delete</span></button></p>';

                                    //Prendi ora tutti i file associati a questa sottocartella:
                                    $query = "SELECT * FROM FAD WHERE IDCartella = '" . $sottoCartella->ID . "';";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    $filesSottoCartella = $stmt;

                                    //Mostra ora tutti i file associati a questa sottocartella:
                                    while ($fileSottoCartella = $filesSottoCartella->fetch(PDO::FETCH_OBJ)) {
                                        //Mostra ora il file associato alla sottocartella:
                                        echo '<p class="fileSubFolderLeTueFAD"><span class="material-icons fileSubFolderLeTueFAD">description</span><strong>' . $fileSottoCartella->Nome . '</strong><button onclick=\'visualizzaAnteprimaDocentiFAD("' . $annoScolastico . '", "' . $materia->Classe . '", "' . $materia->Materia . '", "' . $fileSottoCartella->ID . '", "' . $fileSottoCartella->Estensione_file . '")\' class="funzioniLeTueFAD"><span class="material-icons">not_started</span></button><button onclick=\'cancellaFAD("' . $fileSottoCartella->ID . '")\' class="funzioniLeTueFAD" style="background-color: rgb(175, 99, 99)"><span class="material-icons">delete</span></button></p>';
                                    }
                                }
                            }
                        }
                        //Form nascosto per la creazione della cartella:
                        echo '<form id="creaCartella" action="includes/creaCartella.inc.php" method="post">';
                        echo '<input type="hidden" id="nomeCartella" name="nome" value="" required>';
                        echo '<input type="hidden" id="tipoCartella" name="tipoCartella" value="" required>';
                        echo '<input type="hidden" id="cartellaPrincipale" name="cartellaPrincipale" value="" required>';
                        echo '<input type="hidden" id="materia" name="materia" value="" required>';
                        echo '<input type="hidden" id="classe" name="classe" value="" required>';
                        echo '</form>';

                        //Form nascosto per la cancellazione di cartelle e sottocartelle:
                        echo '<form id="cancellaCartella" action="includes/cancellaCartella.inc.php" method="post">';
                        echo '<input type="hidden" id="IDCartella" name="IDCartella" value="" required>';
                        echo '</form>';

                        //Form nascosto per la cancellazione di una FAD:
                        echo '<form id="cancellaFAD" action="includes/cancellaFAD.inc.php" method="post">';
                        echo '<input type="hidden" id="IDFAD" name="IDFAD" value="" required>';
                        echo '</form>';
                    }
                } catch (PDOException $errore) {
                    //Se c'è stato un errore con il ritorno dei dati dal database, dillo:
                    die("Il ritorno dei dati dal database è fallito: " . $errore);
                }
            ?>
        </div>
    </body>
</html>