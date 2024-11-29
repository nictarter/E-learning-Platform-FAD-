<?php

//Connettiti alla sessione in corso:
session_start();

//Cancella la sessione in corso:
session_destroy();

//Crea una nuova sessione:
session_start();

//Vai a fare il login:
header("Location: ../login.php");