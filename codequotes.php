<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM quotes WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Quote eliminado exitosamente";
        header("Location: quotes.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el quote, contácte a soporte";
        header("Location: quotes.php");
        exit(0);
    }
}

if (isset($_POST['aprobar'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['aprobar']);

            $query = "UPDATE `quotes` SET `estatusq` = '0' WHERE `quotes`.`id` = '$registro_id'";
            $query_run = mysqli_query($con, $query);

            if ($query_run) {
                $_SESSION['message'] = "Quote aprobado exitosamente";
                header("Location: quotes.php");
                exit(0);
            } else {
                $_SESSION['message'] = "Error al aprobar el quote, contacte a soporte";
                header("Location: quotes.php");
                exit(0);
            }
}



if (isset($_POST['save'])) {
    $solicitante = mysqli_real_escape_string($con, $_POST['solicitante']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $proyecto = mysqli_real_escape_string($con, $_POST['proyecto']);
    $cotizacion = mysqli_real_escape_string($con, $_POST['cotizacion']);
    $medio = addslashes(file_get_contents($_FILES['medio']['tmp_name']));

    $query = "INSERT INTO quotes SET solicitante='$solicitante', rol='$rol', proyecto='$proyecto', cotizacion='$cotizacion', estatusq='1', medio='$medio'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo QUOTE: $cotizacion', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Quote creado exitosamente";
        header("Location: quotes.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el quote, contacte a soporte";
        header("Location: quotes.php");
        exit(0);
    }
}
