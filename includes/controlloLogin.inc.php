<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

session_start();

//Se sei arrivato qui come avresti dovuto:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Prendi la mail inserita:
    $email = $_POST["email"];

    //Prova ad effettuare il controllo con il database:
    try {
        //Connettiti al database:
        include_once "../includes/connectDatabase.inc.php";

        //Verifica l'anno scolastico:
        $query = "SELECT Anno_scolastico FROM Anno_scolastico WHERE Terminato = '0';";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION["annoScolastico"] = $result;

        //Controlla se l'utente è un admin:
        $query = "SELECT Email FROM Admin WHERE Email = :email;";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== False) {
            $_SESSION["ruoloAdmin"] = "admin";
            $_SESSION["ruolo"] = "admin"; //Di per sè è inutile, però se non si imposta, nel momento in cui si controlla se l'utente è studente o docente scatta l'errore (bisognerebbe aggiungere un controllo ma è più efficace così)
            $_SESSION["email"] = $email;
        }

        //Controlla se l'utente è un docente:
        $query = "SELECT Email_docente FROM Materie WHERE Email_docente = :email AND Anno_scolastico = '" . implode($_SESSION['annoScolastico']) . "';";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== False) {
            $_SESSION["ruolo"] = "docente";
            $_SESSION["email"] = $email;
            header("Location: ../index.php");
        }

        //Controlla se l'utente è uno studente:
        $query = "SELECT Classe FROM Studenti WHERE Email = :email AND Anno_scolastico = '" . implode($_SESSION['annoScolastico']) . "';";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== False) {
            $_SESSION["ruolo"] = "studente";
            $_SESSION["email"] = $email;
            $_SESSION["classe"] = $result;
            header("Location: ../index.php");
        }

        //Se l'utente risulta essere un admin, vai alla pagina principale, altrimenti torna al login (dato che se è un docente o uno studente viene già rediretto):
        if (isset($_SESSION["ruoloAdmin"]) === True) {
            header("Location: ../index.php");
        } else {
            header("Location: ../login.php");
        }

        //Ferma tutto il processo:
        $db = null;
        $stmt = null;
        die();
    } catch(PDOException $errore) {
        //Se c'è stato un errore con l'inserimento dei dati nel database, dillo:
        die("L'invio dei dati al database è fallito: " . $errore);
    }
} else {
    //Se non sei arrivato qui come avresti dovuto (ad es. digitando il link manualmente nel browser), torna alla homepage
    header("Location: ../index.php");
}