<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM plano WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Maquinado eliminado exitosamente";
        header("Location: maquinados.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el maquinado, contácte a soporte";
        header("Location: maquinados.php");
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

if (isset($_POST['save'])) {
    // Escape other non-array POST values
    $idproyecto = isset($_POST['idproyecto']) ? mysqli_real_escape_string($con, $_POST['idproyecto']) : '';
    $nombreplano = isset($_POST['nombreplano']) ? mysqli_real_escape_string($con, $_POST['nombreplano']) : '';
    if (isset($_FILES['medio']) && $_FILES['medio']['error'] !== UPLOAD_ERR_NO_FILE) {
        $medio = file_get_contents($_FILES['medio']['tmp_name']);
    } else {
        // If no file is uploaded, set medio as an empty string
        $medio = '';
    }
    $nivel = isset($_POST['nivel']) ? mysqli_real_escape_string($con, $_POST['nivel']) : '';
    $piezas = isset($_POST['piezas']) ? mysqli_real_escape_string($con, $_POST['piezas']) : '';
    $actividad = isset($_POST['actividad']) ? mysqli_real_escape_string($con, $_POST['actividad']) : '';

    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
        // Insertar el registro en la tabla `plano` una sola vez fuera del bucle
        $query = "INSERT INTO plano (idproyecto, nombreplano, medio, nivel, piezas, actividad, estatusplano) VALUES (?, ?, ?, ?, ?, ?, '1')";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssis', $idproyecto, $nombreplano, $medio, $nivel, $piezas, $actividad);
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

                    $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo maquinado: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                    $query_rundos = mysqli_query($con, $querydos);
                } else {
                    $_SESSION['message'] = "Error al crear el maquinado, contacte a soporte";
                    header("Location: maquinados.php");
                    exit(0);
                }
            }
            $_SESSION['message'] = "Maquinado creado exitosamente";
                    header("Location: maquinados.php");
                    exit(0);
        } else {
            $_SESSION['message'] = "Error al crear el maquinado, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}
?>