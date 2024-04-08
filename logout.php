<?php
session_start();
require 'dbcon.php';
$idcodigo = $_SESSION['codigo'];
$querysesion = "UPDATE `usuarios` SET `sesion` = '0' WHERE `usuarios`.`codigo` = '$idcodigo'";
$querysesion_run = mysqli_query($con, $querysesion);
if (isset($_SESSION['rol']) && ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
    
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $query_verificar = "SELECT MAX(id) as ultimo FROM asistencia WHERE idcodigo='$idcodigo' AND salida IS NULL";
    $resultado_verificar = mysqli_query($con, $query_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        // Si existe una fila, actualiza la salida
        $fila = mysqli_fetch_assoc($resultado_verificar);
        //$id_asistencia = $fila['id'];
        $ultimo = $fila['ultimo'];

        $query_actualizar = "UPDATE asistencia SET salida='$hora_actual' WHERE id='$ultimo' AND fecha = '$fecha_actual'";
        mysqli_query($con, $query_actualizar);
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        $message = "Salida de " . $nombre . ' ' . $apellidop . ', ' . "no se registro correctamente, notifique a RRHH, hora de salida: " . $hora_actual;
        $_SESSION['message'] = $message;
        header("Location: dashboard.php");
        exit();
    }
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}
