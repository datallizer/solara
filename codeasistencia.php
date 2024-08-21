<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['propio'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $salida = mysqli_real_escape_string($con, $_POST['salida']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);

    $query = "UPDATE `asistencia` SET `salida` = '$salida' WHERE `asistencia`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Hora de salida actualizada exitosamente";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al actualizar hora de salida, contácte a soporte";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    }
}

if (isset($_POST['solicitar'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $salida = mysqli_real_escape_string($con, $_POST['salidadetail']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);

    $query = "UPDATE `asistencia` SET `salida` = '$salida', `estatus` = '1' WHERE `asistencia`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Solicitud enviada exitosamente";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al enviar la solicitud, contácte a soporte";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    }
}

if (isset($_POST['aprobar'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);

    $query = "UPDATE `asistencia` SET `estatus` = '0' WHERE `asistencia`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Hora de salida aprobada exitosamente";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar la hora de salida, contácte a soporte";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    }
}

if (isset($_POST['rechazar'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);

    $query = "UPDATE `asistencia` SET `estatus` = '2' WHERE `asistencia`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Hora de salida rechazada, se notifico a RRHH para su revisión";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    } else {
        $_SESSION['message'] = "Error rechazar la hora de salida, contácte a soporte";
        header("Location: asistenciapersonal.php?id=$codigo");
        exit(0);
    }
}

?>