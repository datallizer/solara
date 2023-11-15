<?php
    session_destroy(); // Elimino la sesion
    header("Location: login.php"); //redirijo al index
    exit();
?>