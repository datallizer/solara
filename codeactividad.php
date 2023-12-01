<?php
require 'dbcon.php';
session_start();
if (isset($_POST['save'])) {
    $idcodigo = $_SESSION['codigo'];
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Operdador(a) $nombre $apellidop detuvo la actividad, $nombreplano por motivo $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Se detuvo la actividad exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el paro, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}
