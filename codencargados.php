<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_POST['deletecontrol'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deletecontrol']);

    $query = "DELETE FROM asignaciondiagrama WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Técnico de control eliminado exitosamente";
        header("Location: ensamble.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al técnio de mecánica, contácte a soporte";
        header("Location: asignaciondiagrama.php");
        exit(0);
    }
}

if (isset($_POST['deleteproyecto'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deleteproyecto']);

    $query = "DELETE FROM encargadoproyecto WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Encargado de proyecto eliminado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al encargado de proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if (isset($_POST['deleteplano'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deleteplano']);

    $query = "DELETE FROM asignacionplano WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Operador eliminado exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el operador, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['deleteingeniero'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deleteingeniero']);

    $query = "DELETE FROM asignacioningenieria WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Ingeniero eliminado exitosamente";
        header("Location: ingenieria.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el ingeniero, contacte a soporte";
        header("Location: ingenieria.php");
        exit(0);
    }
}

?>