<?php

/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

//Connettiti alla sessione in corso:
session_start();

//Cancella la sessione in corso:
session_destroy();

//Crea una nuova sessione:
session_start();

//Vai a fare il login:
header("Location: ../login.php");