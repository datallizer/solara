<?php
require 'dbcon.php';
session_start();
if (isset($_POST['save'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Detuvo la actividad, $nombreplano por motivo $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Se detuvo la actividad exitosamente, motivo $motivosparo";
        header("Location: inicioactividades.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el paro, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['restart'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);

    $query = "UPDATE `plano` SET `estatusplano` = '1' WHERE `plano`.`id` = '$id'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        header("Location: inicioactividades.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al reiniciar el paro, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['finish'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Termino el plano, $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '0' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "$nombre, terminaste el plano $nombreplano exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el plano, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}
?>