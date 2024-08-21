<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM motivos WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Motivo eliminado exitosamente";
        header("Location: motivos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "UPDATE `motivos` SET `motivosparo` = '$motivosparo' WHERE `motivos`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para maquinados: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo editado exitosamente";
        header("Location: motivos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    $mensaje = mysqli_real_escape_string($con, $_POST['mensaje']);
    
    // Verificar si 'codigooperador' está definido y es un array
    $codigosOperadores = isset($_POST['codigooperador']) ? $_POST['codigooperador'] : [];
    
    // Convertir el array en una cadena para la consulta SQL
    $idcodigo = implode(',', array_map('intval', $codigosOperadores)); // Convierte a enteros para evitar inyecciones SQL

    $emisor = $_SESSION['codigo'];
    $fecha = date("Y-m-d");
    $hora = date("H:i");
    $estatus = '1';

    $query = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha', '$hora', '$estatus')";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Motivo creado exitosamente";
        header("Location: dashboard.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el motivo, contacte a soporte";
        header("Location: dashboard.php");
        exit(0);
    }
}
