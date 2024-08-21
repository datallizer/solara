<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_POST['delete']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM actividadesmecanica WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Actividad para T. Mecánico eliminada exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar la actividad para T. Mecánico, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

if(isset($_POST['update']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $actividad = mysqli_real_escape_string($con, $_POST['actividad']);

    $query = "UPDATE `actividadesmecanica` SET `actividad` = '$actividad' WHERE `actividadesmecanica`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito una actividad para T. Mecánico: $actividad', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Actividad editado exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar la actividad para T. Mecánico, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

if(isset($_POST['save']))
{
    $actividad = mysqli_real_escape_string($con, $_POST['actividad']);

    $query = "INSERT INTO actividadesmecanica SET actividad='$actividad'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro una nueva actividad para T. Mecánico: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Actividad para T. Mecánico creada exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear la actividad para T. Mecánico, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

if(isset($_POST['deletecontrol']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['deletecontrol']);

    $query = "DELETE FROM actividadescontrol WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Actividad para T. Control eliminada exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar la actividad para T. Control, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

if(isset($_POST['updatecontrol']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $actividad = mysqli_real_escape_string($con, $_POST['actividad']);

    $query = "UPDATE `actividadescontrol` SET `actividad` = '$actividad' WHERE `actividadescontrol`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito una actividad para T. Control: $actividad', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Actividad editado exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar la actividad para T. Control, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

if(isset($_POST['savecontrol']))
{
    $actividad = mysqli_real_escape_string($con, $_POST['actividad']);

    $query = "INSERT INTO actividadescontrol SET actividad='$actividad'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro una nueva actividad para T. Control: $motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Actividad para T. Control creada exitosamente";
        header("Location: actividades.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear la actividad para T. Control, contacte a soporte";
        header("Location: actividades.php");
        exit(0);
    }
}

?>