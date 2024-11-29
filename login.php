<?php
    session_start();
    if (isset($_SESSION["ruolo"]) === true or isset($_SESSION["ruoloAdmin"]) === true) {
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
        <h1>Accedi ora!</h1>
        <div class="login">
            <form action="includes/controlloLogin.inc.php" method="post">
                <input type="email" class="login" name="email" placeholder="Email" required>
                <button>Accedi</button>
            </form>
        </div>
    </body>
</html>