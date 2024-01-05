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
        $_SESSION['message'] = "Reiniciaste actividades exitosamente";
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

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Termino el maquinado, $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '0' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "$nombre, terminaste el maquinado $nombreplano exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el maquinado, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['pausar'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Se pauso el maquinado $nombreplano por motivo: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "$nombre, pausaste el maquinado: $nombreplano";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el maquinado, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['saveensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Detuvo la actividad de ensamble, $nombreplano por motivo $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Se detuvo la actividad de ensamble exitosamente, motivo $motivosparo";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar el paro, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}

if (isset($_POST['restartensamble'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);

    $query = "UPDATE `diagrama` SET `estatusplano` = '1' WHERE `diagrama`.`id` = '$id'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Reiniciaste actividades de ensamble exitosamente";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al reiniciar el paro, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}

if (isset($_POST['finishensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Termino el ensamble, $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '0' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "$nombre, terminaste el ensamble $nombreplano exitosamente";
        header("Location: ensamble.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el ensamble, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}

if (isset($_POST['pausarensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Se pauso el ensamble $nombreplano por motivo: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "$nombre, pausaste el ensamble: $nombreplano";
        header("Location: ensamble.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el ensamble, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}
?>