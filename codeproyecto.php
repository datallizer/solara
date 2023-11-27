<?php
require 'dbcon.php';

if(isset($_POST['delete']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM proyecto WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Proyecto eliminado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar el proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if(isset($_POST['update']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    // Encriptar la nueva contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE `proyecto` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `password` = '$hashed_password', `rol` = '$rol' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Proyecto editado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar el proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if(isset($_POST['save']))
{
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $cliente = mysqli_real_escape_string($con, $_POST['cliente']);
    $prioridad = mysqli_real_escape_string($con, $_POST['prioridad']);
    $fechainicio = mysqli_real_escape_string($con, $_POST['fechainicio']);
    $fechafin = mysqli_real_escape_string($con, $_POST['fechafin']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);
    $presupuesto = mysqli_real_escape_string($con, $_POST['presupuesto']);
    $etapadiseño = mysqli_real_escape_string($con, $_POST['etapadiseño']);
    $etapacontrol = mysqli_real_escape_string($con, $_POST['etapacontrol']);
    $estatus = '1';
 // Verify if checkboxes are selected and process each value
 if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
    // Insertar el registro en la tabla `plano` una sola vez fuera del bucle
    // $query = "INSERT INTO proyecto SET nombre='$nombre', cliente='$cliente', prioridad='$prioridad', fechainicio='$fechainicio', fechafin='$fechafin', detalles='$detalles', presupuesto='$presupuesto', estatus='1',etapadiseño='$etapadiseño',etapacontrol='$etapacontrol'";
    
    $query = "INSERT INTO proyecto (nombre, cliente, prioridad, fechainicio, fechafin, detalles, presupuesto, estatus, etapadiseño, etapacontrol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssssi', $nombre, $cliente, $prioridad, $fechainicio, $fechafin, $detalles, $presupuesto, $estatus, $etapadiseño, $etapacontrol);
            mysqli_stmt_execute($stmt);

            $idproyecto = mysqli_insert_id($con);
            foreach ($_POST['codigooperador'] as $codigoOperador) {
                // Insertar en la tabla `asignacionplano` utilizando el ID obtenido anteriormente
                $queryplano = "INSERT INTO encargadoproyecto (idproyecto, codigooperador) VALUES (?, ?)";
                $stmtPlano = mysqli_prepare($con, $queryplano);

                if ($stmtPlano) {
                    mysqli_stmt_bind_param($stmtPlano, 'ii', $idproyecto, $codigoOperador);
                    mysqli_stmt_execute($stmtPlano);
                } else {
                    $_SESSION['message'] = "Error al crear el proyecto, contacte a soporte";
                    header("Location: proyectos.php");
                    exit(0);
                }
            }

            $_SESSION['message'] = "Proyecto creado exitosamente";
            header("Location: proyectos.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear el plano, contacte a soporte";
            header("Location: proyectos.php");
            exit(0);
        }
    }
}

?>