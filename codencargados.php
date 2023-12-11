<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM encargadomecanico WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Técnico de mecánica eliminado exitosamente";
        header("Location: encargadomecanico.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al técnico de mecánica, contácte a soporte";
        header("Location: encargadomecanico.php");
        exit(0);
    }
}

if (isset($_POST['deletetcontrol'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deletetcontrol']);

    $query = "DELETE FROM encargadotcontrol WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Técnico de control eliminado exitosamente";
        header("Location: encargadocontrol.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al técnio de mecánica, contácte a soporte";
        header("Location: encargadocontrol.php");
        exit(0);
    }
}

if (isset($_POST['deleteproyecto'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deleteproyecto']);

    $query = "DELETE FROM encargadoproyecto WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Encargado de proyecto eliminado exitosamente";
        header("Location: encargadoproyecto.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al encargado de proyecto, contácte a soporte";
        header("Location: encargadoproyecto.php");
        exit(0);
    }
}

if (isset($_POST['deleteplano'])) {
    $id_encargado = mysqli_real_escape_string($con, $_POST['deleteplano']);

    $query = "DELETE FROM asignacionplano WHERE id='$id_encargado' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Operador asignado a maquinado eliminado exitosamente";
        header("Location: encargadoplanos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar al operador, contácte a soporte";
        header("Location: encargadoplanos.php");
        exit(0);
    }
}

?>