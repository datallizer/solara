<?php
session_start();
require 'dbcon.php';

if (isset($_SESSION['rol']) && ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)) {
    $idcodigo = $_SESSION['codigo'];
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $query_verificar = "SELECT * FROM asistencia WHERE idcodigo='$idcodigo' AND salida IS NULL";
    $resultado_verificar = mysqli_query($con, $query_verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        // Si existe una fila, actualiza la salida
        $fila = mysqli_fetch_assoc($resultado_verificar);
        $id_asistencia = $fila['id']; // Suponiendo que el ID de la asistencia se llama 'id'

        $query_actualizar = "UPDATE asistencia SET salida='$hora_actual' WHERE id='$id_asistencia'";
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
} else{
        session_destroy();
        header("Location: login.php");
        exit();
}
?>