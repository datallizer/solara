<?php
require 'dbcon.php';
if (isset($_POST['save'])) {
    $idcodigo = mysqli_real_escape_string($con, $_SESSION['codigo']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i:s"); // Obtener hora actual en formato Hora:Minutos:Segundos

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Paro creado exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el paro, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}
