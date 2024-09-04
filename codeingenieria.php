<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM ingenieria WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);
    $queryingenieria = "DELETE FROM asignacioningenieria WHERE idplano='$registro_id' ";
    $queryingenieria_run = mysqli_query($con, $queryingenieria);

    if ($query_run) {
        $_SESSION['message'] = "Actividad eliminada exitosamente";
        header("Location: ingenieria.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar la actividad, contácte a soporte";
        header("Location: ingenieria.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombreplano = mysqli_real_escape_string($con, $_POST['nombreplano']);
    $nivel = mysqli_real_escape_string($con, $_POST['nivel']);
    $piezas = mysqli_real_escape_string($con, $_POST['piezas']);
    $estatusplano = mysqli_real_escape_string($con, $_POST['estatusplano']);
    $actividad = mysqli_real_escape_string($con, $_POST['actividad']);

    $query = "UPDATE `plano` SET `nombreplano` = '$nombreplano', `nivel` = '$nivel', `piezas` = '$piezas', `estatusplano` = '$estatusplano', `actividad` = '$actividad'";

    if (isset($_FILES['medio']) && $_FILES['medio']['error'] == UPLOAD_ERR_OK) {
        $medio = file_get_contents($_FILES['medio']['tmp_name']);
        $query .= ", `medio` = '" . mysqli_real_escape_string($con, $medio) . "'";
    }

    $query .= " WHERE `plano`.`id` = '$id'";

    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Ensamble $nombreplano editado exitosamente";
    } else {
        $_SESSION['message'] = "Error al editar el ensamble $nombreplano, contácte a soporte: " . mysqli_error($con);
    }
    header("Location: maquinados.php");
    exit(0);
}

if (isset($_POST['finalizar'])) {
    $id = mysqli_real_escape_string($con, $_POST['finalizar']);
    $estatusplano = 0;

    $query = "UPDATE `ingenieria` SET `estatusplano` = '$estatusplano' WHERE `ingenieria`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Actividad finalizada exitosamente";
        header("Location: ingenieria.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al finalizar la actividad";
        header("Location: ingenieria.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    // Escape other non-array POST values
    $idproyecto = isset($_POST['idproyecto']) ? mysqli_real_escape_string($con, $_POST['idproyecto']) : '';
    $nombreplano = isset($_POST['nombreplano']) ? mysqli_real_escape_string($con, $_POST['nombreplano']) : '';
    $prioridad = isset($_POST['prioridad']) ? mysqli_real_escape_string($con, $_POST['prioridad']) : '';
    $actividad = isset($_POST['actividad']) ? mysqli_real_escape_string($con, $_POST['actividad']) : '';

    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
        $query = "INSERT INTO ingenieria (idproyecto, nombreplano, prioridad, actividad, estatusplano) VALUES (?, ?, ?, ?, '1')";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'isis', $idproyecto, $nombreplano, $prioridad, $actividad);
            mysqli_stmt_execute($stmt);

            $idplano = mysqli_insert_id($con);

            foreach ($_POST['codigooperador'] as $codigoOperador) {
                // Insertar en la tabla `asignacionplano` utilizando el ID obtenido anteriormente
                $queryplano = "INSERT INTO asignacioningenieria (idplano, codigooperador) VALUES (?, ?)";
                $stmtPlano = mysqli_prepare($con, $queryplano);

                if ($stmtPlano) {
                    mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                    mysqli_stmt_execute($stmtPlano);
                    $idcodigo = $_SESSION['codigo'];
                    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

                    $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo maquinado: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                    $query_rundos = mysqli_query($con, $querydos);

                    $mensaje = 'Tienes una nueva actividad de ingeniería, Nombre: ' . $nombreplano . ' Actividad: ' . $actividad  . ' Prioridad: ' . $prioridad;

                    $idcodigo = $codigoOperador;

                    $emisor = $_SESSION['codigo'];
                    $estatus = '1';

                    $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                    $querymensajes_run = mysqli_query($con, $querymensajes);
                } else {
                    $_SESSION['message'] = "Error al crear la actividad, contacte a soporte";
                    header("Location: ingenieria.php");
                    exit(0);
                }
            }
            $_SESSION['message'] = "Actividad creada exitosamente";
            header("Location: ingenieria.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear la actividad, contacte a soporte";
            header("Location: ingenieria.php");
            exit(0);
        }
    } else {
        $query = "INSERT INTO ingenieria (idproyecto, nombreplano, prioridad, actividad, estatusplano) VALUES (?, ?, ?, ?, '1')";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'isis', $idproyecto, $nombreplano, $prioridad, $actividad);
            mysqli_stmt_execute($stmt);

            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
            $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

            $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo maquinado: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);

            $_SESSION['message'] = "Actividad creada exitosamente";
            header("Location: ingenieria.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear la actividad, contacte a soporte";
            header("Location: ingenieria.php");
            exit(0);
        }
    }
}
