<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM proyecto WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Proyecto eliminado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $cliente = mysqli_real_escape_string($con, $_POST['cliente']);
    $presupuesto = mysqli_real_escape_string($con, $_POST['presupuesto']);
    $fechainicio = mysqli_real_escape_string($con, $_POST['fechainicio']);
    $fechafin = mysqli_real_escape_string($con, $_POST['fechafin']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);
    $prioridad = mysqli_real_escape_string($con, $_POST['prioridad']);
    $etapadiseño = mysqli_real_escape_string($con, $_POST['etapadiseño']);
    $etapacontrol = mysqli_real_escape_string($con, $_POST['etapacontrol']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);

    $query = "UPDATE `proyecto` SET `nombre` = '$nombre', `cliente` = '$cliente', `presupuesto` = '$presupuesto', `fechainicio` = '$fechainicio', `fechafin` = '$fechafin', `estatus` = '$estatus', `prioridad` = '$prioridad', `etapadiseño` = '$etapadiseño', `etapacontrol` = '$etapacontrol', `detalles` = '$detalles' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Proyecto editado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if (isset($_POST['archivar'])) {
    $id = mysqli_real_escape_string($con, $_POST['archivar']);
    $estatus = 0;

    $query = "UPDATE `proyecto` SET `estatus` = '$estatus' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Proyecto editado exitosamente";
        header("Location: proyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el proyecto, contácte a soporte";
        header("Location: proyectos.php");
        exit(0);
    }
}

if (isset($_POST['etapas'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $etapamecanica = mysqli_real_escape_string($con, $_POST['etapamecanica']);
    $etapatcontrol = mysqli_real_escape_string($con, $_POST['etapatcontrol']);
    $etapadiseño = mysqli_real_escape_string($con, $_POST['etapadiseño']);
    $etapacontrol = mysqli_real_escape_string($con, $_POST['etapacontrol']);

    $query = "UPDATE `proyecto` SET `etapamecanica` = '$etapamecanica', `etapatcontrol` = '$etapatcontrol', `etapadiseño` = '$etapadiseño', `etapacontrol` = '$etapacontrol' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Etapa actualizada exitosamente";
        header("Location: dashboard.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al actualizar la etapa, contacte a soporte";
        header("Location: dashboard.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $cliente = mysqli_real_escape_string($con, $_POST['cliente']);
    $prioridad = mysqli_real_escape_string($con, $_POST['prioridad']);
    $fechainicio = mysqli_real_escape_string($con, $_POST['fechainicio']);
    $fechafin = mysqli_real_escape_string($con, $_POST['fechafin']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);
    $presupuesto = mysqli_real_escape_string($con, $_POST['presupuesto']);
    $etapadiseño = mysqli_real_escape_string($con, $_POST['etapadiseño']);
    $etapacontrol = mysqli_real_escape_string($con, $_POST['etapacontrol']);
    $etapatcontrol = mysqli_real_escape_string($con, $_POST['etapatcontrol']);
    $etapamecanica = mysqli_real_escape_string($con, $_POST['etapamecanica']);
    $estatus = '1';
    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
        // Insertar el registro en la tabla `plano` una sola vez fuera del bucle
        // $query = "INSERT INTO proyecto SET nombre='$nombre', cliente='$cliente', prioridad='$prioridad', fechainicio='$fechainicio', fechafin='$fechafin', detalles='$detalles', presupuesto='$presupuesto', estatus='1',etapadiseño='$etapadiseño',etapacontrol='$etapacontrol'";

        $query = "INSERT INTO proyecto (nombre, cliente, prioridad, fechainicio, fechafin, detalles, presupuesto, estatus, etapadiseño, etapacontrol, etapamecanica, etapatcontrol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssssssi', $nombre, $cliente, $prioridad, $fechainicio, $fechafin, $detalles, $presupuesto, $estatus, $etapadiseño, $etapacontrol, $etapatcontrol, $etapamecanica);
            mysqli_stmt_execute($stmt);

            $idproyecto = mysqli_insert_id($con);
            foreach ($_POST['codigooperador'] as $codigoOperador) {
                // Insertar en la tabla `encargadoproyecto` utilizando el ID obtenido anteriormente
                $queryplano = "INSERT INTO encargadoproyecto (idproyecto, codigooperador) VALUES (?, ?)";
                $stmtPlano = mysqli_prepare($con, $queryplano);

                if ($stmtPlano) {
                    mysqli_stmt_bind_param($stmtPlano, 'ii', $idproyecto, $codigoOperador);
                    mysqli_stmt_execute($stmtPlano);

                    $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                    $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                    $mensaje = 'Tienes un nuevo proyecto asignado, Nombre: ' . $nombre . ' Detalles: ' . $detalles  . ' Prioridad: ' . $prioridad;

                    $idcodigo = $codigoOperador;

                    $emisor = $_SESSION['codigo'];
                    $estatus = '1';

                    $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                    $querymensajes_run = mysqli_query($con, $querymensajes);
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