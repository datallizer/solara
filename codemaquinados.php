<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM plano WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Plano eliminado exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el plano, contácte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    $query = "UPDATE `plano` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `password` = '$hashed_password', `rol` = '$rol' WHERE `plano`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Plano editado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el plano, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    // Escape other non-array POST values
    $idproyecto = isset($_POST['idproyecto']) ? mysqli_real_escape_string($con, $_POST['idproyecto']) : '';
    $nombreplano = isset($_POST['nombreplano']) ? mysqli_real_escape_string($con, $_POST['nombreplano']) : '';
    $medio = file_get_contents($_FILES['medio']['tmp_name']);
    $nivel = isset($_POST['nivel']) ? mysqli_real_escape_string($con, $_POST['nivel']) : '';
    $piezas = isset($_POST['piezas']) ? mysqli_real_escape_string($con, $_POST['piezas']) : '';

    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
        // Insertar el registro en la tabla `plano` una sola vez fuera del bucle
        $query = "INSERT INTO plano (idproyecto, nombreplano, medio, nivel, piezas, estatusplano) VALUES (?, ?, ?, ?, ?, '1')";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssi', $idproyecto, $nombreplano, $medio, $nivel, $piezas);
            mysqli_stmt_execute($stmt);

            // Obtener el ID del último registro insertado en la tabla `plano`
            $idplano = mysqli_insert_id($con);

            foreach ($_POST['codigooperador'] as $codigoOperador) {
                // Insertar en la tabla `asignacionplano` utilizando el ID obtenido anteriormente
                $queryplano = "INSERT INTO asignacionplano (idplano, codigooperador) VALUES (?, ?)";
                $stmtPlano = mysqli_prepare($con, $queryplano);

                if ($stmtPlano) {
                    mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                    mysqli_stmt_execute($stmtPlano);
                    $idcodigo = $_SESSION['codigo'];
                    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

                    $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo plano: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                    $query_rundos = mysqli_query($con, $querydos);

                    $_SESSION['message'] = "Plano creado exitosamente";
                    header("Location: maquinados.php");
                    exit(0);
                } else {
                    $_SESSION['message'] = "Error al crear el plano, contacte a soporte";
                    header("Location: maquinados.php");
                    exit(0);
                }
            }
        } else {
            $_SESSION['message'] = "Error al crear el plano, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}
