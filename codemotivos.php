<?php
require 'dbcon.php';
session_start();

if(isset($_POST['delete']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM motivos WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Motivo eliminado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['update']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "UPDATE `motivos` SET `motivosparo` = '$motivosparo' WHERE `motivos`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para maquinados: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo editado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['save']))
{
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "INSERT INTO motivos SET motivosparo='$motivosparo'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo motivo de paro para maquinados: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo creado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear el motivo, contacte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['deleteensamble']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['deleteensamble']);

    $query = "DELETE FROM motivosensamble WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Motivo eliminado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['deleteinicio']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['deleteinicio']);

    $query = "DELETE FROM motivosinicio WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Motivo eliminado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar el motivo, contacte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['updateensamble']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "UPDATE `motivosensamble` SET `motivosparo` = '$motivosparo' WHERE `motivosensamble`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para ensamble: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo editado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['updateinicio']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $motivo = mysqli_real_escape_string($con, $_POST['motivo']);

    $query = "UPDATE `motivosinicio` SET `motivo` = '$motivo' WHERE `motivosinicio`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un motivo de paro para ensamble: $motivo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo editado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar el motivo, contácte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['saveensamble']))
{
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);

    $query = "INSERT INTO motivosensamble SET motivosparo='$motivosparo'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo motivo de paro para ensamble: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo creado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear el motivo, contacte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

if(isset($_POST['saveinicio']))
{
    $motivo = mysqli_real_escape_string($con, $_POST['motivo']);

    $query = "INSERT INTO motivosinicio SET motivo='$motivo'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo motivo de paro para ensamble: $motivo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Motivo creado exitosamente";
        header("Location: motivos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear el motivo, contacte a soporte";
        header("Location: motivos.php");
        exit(0);
    }
}

?>