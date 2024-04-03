<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM permisos WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Permiso eliminado exitosamente";
        header("Location: permisos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el permiso, contacte a soporte";
        header("Location: permisos.php");
        exit(0);
    }
}

if (isset($_POST['aprobar'])) {
    $id = mysqli_real_escape_string($con, $_POST['aprobar']);

    $query = "UPDATE `permisos` SET `estatus` = '2' WHERE `permisos`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para per: $id', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Permiso aprobado exitosamente $id";
        header("Location: permisos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar el permiso, contacte a soporte";
        header("Location: permisos.php");
        exit(0);
    }
}

if (isset($_POST['rechazar'])) {
    $id = mysqli_real_escape_string($con, $_POST['rechazar']);

    $query = "UPDATE `permisos` SET `estatus` = '0' WHERE `permisos`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para maquinados: $id', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Permiso rechazado exitosamente";
        header("Location: motivos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al rechazar el permiso, contacte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    $idcodigo = mysqli_real_escape_string($con, $_POST['idcodigo']);
    $detalle = mysqli_real_escape_string($con, $_POST['detalle']);
    $fecha = mysqli_real_escape_string($con, $_POST['fecha']);
    $fecha_fin = mysqli_real_escape_string($con, $_POST['fecha_fin']);
    $tiempo = mysqli_real_escape_string($con, $_POST['tiempo']);

    // Convertir las fechas a objetos DateTime
    $fecha_inicio = new DateTime($fecha);
    $fecha_fin_obj = new DateTime($fecha_fin);

    // Recorrer cada fecha entre fecha y fecha_fin
    $intervalo = new DateInterval('P1D'); // Intervalo de un día
    // Agregar un día a la fecha de término para incluirla en el rango
    $fecha_fin_obj->modify('+1 day');
    $fechas = new DatePeriod($fecha_inicio, $intervalo, $fecha_fin_obj);

    foreach ($fechas as $fecha_actual) {
        $fecha_actual_str = $fecha_actual->format('Y-m-d');

        // Insertar el registro en la base de datos para esta fecha
        $query = "INSERT INTO permisos (idcodigo, detalle, fecha, tiempo, estatus) VALUES ('$idcodigo', '$detalle', '$fecha_actual_str', '$tiempo', '1')";

        $query_run = mysqli_query($con, $query);
        if (!$query_run) {
            $_SESSION['message'] = "Error al solicitar el permiso, contacte a soporte";
            header("Location: permisos.php");
            exit(0);
        }
    }

    $_SESSION['message'] = "Permisos solicitados exitosamente";
    header("Location: permisos.php");
    exit(0);
}
