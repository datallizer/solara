<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Usuario eliminado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el usuario, contácte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);
    // Obtener la nueva imagen cargada
    if ($_FILES['nuevaFoto']['size'] > 0) {
        $medio = file_get_contents($_FILES['nuevaFoto']['tmp_name']);
        $medio = mysqli_real_escape_string($con, $medio);

        // Actualizar la imagen en la base de datos
        $update_query = "UPDATE usuarios SET medio='$medio' WHERE id='$id'";
        $update_result = mysqli_query($con, $update_query);

        if ($update_result) {
            $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `codigo` = '$codigo', `rol` = '$rol', `estatus` = '$estatus' WHERE `usuarios`.`id` = '$id'";
            $query_run = mysqli_query($con, $query);

            if ($query_run) {
                $idcodigo = $_SESSION['codigo'];
                $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
                $query_rundos = mysqli_query($con, $querydos);
                $_SESSION['message'] = "Usuario editado exitosamente";
                header("Location: usuarios.php");
                exit(0);
            } else {
                $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
                header("Location: usuarios.php");
                exit(0);
            }
        } else {
            $_SESSION['message'] = "Error al actualizar la imagen del usuario, contácte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    } else {
        $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `codigo` = '$codigo', `rol` = '$rol', `estatus` = '$estatus' WHERE `usuarios`.`id` = '$id'";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
            $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
            $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $_SESSION['message'] = "Usuario editado exitosamente";
            header("Location: usuarios.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    }
}

if (isset($_POST['nominaupdate'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nomina = mysqli_real_escape_string($con, $_POST['nomina']);
    // Obtener la nueva imagen cargada
    $query = "UPDATE `usuarios` SET `nomina` = '$nomina' WHERE `usuarios`.`id` = '$id'";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
            $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
            $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito la nómina, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $_SESSION['message'] = "Nómina editado exitosamente";
            header("Location: nomina.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al editar la nómina, contacte a soporte";
            header("Location: nomina.php");
            exit(0);
        }
}



if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $medio = addslashes(file_get_contents($_FILES['medio']['tmp_name']));

    $query = "INSERT INTO usuarios SET nombre='$nombre', apellidop='$apellidop', apellidom='$apellidom', codigo='$codigo', estatus='1', rol='$rol',medio='$medio'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Usuario creado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el usuario, contacte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}
