<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['save'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Detuvo el maquinado, motivo: $motivosparo' WHERE `usuarios`.`codigo` = '$idcodigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
        $_SESSION['message'] = "Se detuvo el maquinado exitosamente, motivo $motivosparo";
        $_SESSION['paro'] = $motivosparo;
        header("Location: inicioactividades.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al detener el maquinado, contacte a soporte";
        header("Location: maquinados.php");
        exit(0);
    }
}

if (isset($_POST['restart'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $paro = isset($_SESSION['paro']) ? $_SESSION['paro'] : '';


    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialoperadores WHERE idcodigo = $idcodigo AND idplano = $id";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    if ($max_id) {
        // Actualizar solo la fila con el ID obtenido
        $query = "UPDATE historialoperadores SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
        $query_run = mysqli_query($con, $query);
    }


    if ($query_run && $paro == 'Lunch') {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio su jornada, maquinado en progreso' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $querytres = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_reingreso = mysqli_query($con, $querytres);
            $_SESSION['message'] = "Reiniciaste el maquinado exitosamente, tu reingreso es a las " . $hora_actual;
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un maquinado pero hubo un error al reiniciar la jornada laboral' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Se reinicio el maquinado pero no su jornada laboral, contacte a soporte";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        }
    } else if ($query_run && $paro != 'Lunch') {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un maquinado y esta en progreso' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Maquinado reiniciado exitosamente";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el maquinado y actualizar el historial, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    } else {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un maquinado pero no se pudo guardar el historial para estadística' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Maquinado reiniciado exitosamente, error al actualizar el historial.";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el maquinado y actualizar el historial, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}


if (isset($_POST['start'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='Inicio', hora='$hora_actual', fecha='$fecha_actual'";
    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);
        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Maquinado en progreso' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Se inicio el maquinado exitosamente";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el maquinado, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}

if (isset($_POST['start'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='Inicio', hora='$hora_actual', fecha='$fecha_actual'";
    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);
        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Maquinado en progreso' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Se inicio el maquinado exitosamente";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el maquinado, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}

if (isset($_POST['finish'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    // Verificar si ya existe un registro con motivo "Inicio" para el mismo `idcodigo` y `idplano`
    $query_check_inicio = "SELECT * FROM historialoperadores WHERE idcodigo = '$idcodigo' AND idplano = '$id' AND motivoactividad = 'Inicio'";
    $result_check_inicio = mysqli_query($con, $query_check_inicio);

    if (mysqli_num_rows($result_check_inicio) == 0) {
        // No existe registro con motivo "Inicio", buscar la fila con el menor ID para el mismo `idplano`
        $query_min_id = "SELECT * FROM historialoperadores WHERE idplano = '$id' ORDER BY id ASC LIMIT 1";
        $result_min_id = mysqli_query($con, $query_min_id);
        $fecha_nueva = $fecha_actual;
        $hora_nueva = date("H:i", strtotime("-1 hour", strtotime($hora_actual))); // Hora actual menos 1 hora

        if (mysqli_num_rows($result_min_id) > 0) {
            // Existe una fila con el mismo `idplano`, tomar su fecha y hora
            $row_min_id = mysqli_fetch_assoc($result_min_id);
            $fecha_nueva = $row_min_id['fecha'];
            $hora_nueva = $row_min_id['hora'];
        }

        // Insertar el nuevo registro con los valores calculados
        $query_insert = "INSERT INTO historialoperadores (idcodigo, idplano, motivoactividad, fecha, hora) 
                         VALUES ('$idcodigo', '$id', 'Inicio', '$fecha_nueva', '$hora_nueva')";
        $result_insert = mysqli_query($con, $query_insert);

        if (!$result_insert) {
            $_SESSION['message'] = "Error al crear el registro. Por favor, contacte a soporte.";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        }
    }

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialoperadores WHERE idcodigo = $idcodigo AND idplano = $id AND motivoactividad = 'Inicio'";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialoperadores SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '0' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);
        // Mover la fila de `plano` a `archivoplano`
        $query_archivar_plano = "INSERT INTO archivoplano SELECT * FROM plano WHERE id = '$id'";
        $query_archivar_plano_run = mysqli_query($con, $query_archivar_plano);

        if ($query_archivar_plano_run) {
            // Eliminar la fila de `plano`
            $query_eliminar_plano = "DELETE FROM plano WHERE id = '$id'";
            $query_eliminar_plano_run = mysqli_query($con, $query_eliminar_plano);

            if ($query_eliminar_plano_run) {
                // Mover la fila de `historialoperadores` a `archivohistorialoperadores`
                $query_archivar_historial = "INSERT INTO archivohistorialoperadores SELECT * FROM historialoperadores WHERE idplano = '$id'";
                $query_archivar_historial_run = mysqli_query($con, $query_archivar_historial);

                if ($query_archivar_historial_run) {
                    // Eliminar la fila de `historialoperadores`
                    $query_eliminar_historial = "DELETE FROM historialoperadores WHERE idplano = '$id'";
                    $query_eliminar_historial_run = mysqli_query($con, $query_eliminar_historial);

                    $query_archivo_plano = "INSERT INTO archivoasignacionplano SELECT * FROM asignacionplano WHERE idplano IN (SELECT id FROM archivoplano)";
                    $query_archivo_plano_run = mysqli_query($con, $query_archivo_plano);

                    $query_delete_asignacion = "DELETE FROM asignacionplano WHERE idplano IN (SELECT id FROM archivoplano)";
                    $query_delete_asignacion_run = mysqli_query($con, $query_delete_asignacion);

                    if ($query_eliminar_historial_run) {
                        $_SESSION['message'] = "Maquinado terminado y archivado exitosamente";
                        header("Location: maquinados.php");
                        exit(0);
                    } else {
                        $_SESSION['message'] = "Error al eliminar historial de operadores, contacte a soporte";
                        header("Location: inicioactividades.php?id=$id");
                        exit(0);
                    }
                } else {
                    $_SESSION['message'] = "Error al archivar historial de operadores, contacte a soporte";
                    header("Location: inicioactividades.php?id=$id");
                    exit(0);
                }
            } else {
                $_SESSION['message'] = "Error al eliminar el plano, contacte a soporte";
                header("Location: inicioactividades.php?id=$id");
                exit(0);
            }
        } else {
            $_SESSION['message'] = "Error al archivar el plano, contacte a soporte";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        }
    }
}


if (isset($_POST['pausar'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        if ($motivosparo == "Fin de jornada laboral") {
            $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
            $query_rundos = mysqli_query($con, $querydos);
            header("Location: logout.php");
            exit(0);
        } elseif ($motivosparo != "Fin de jornada laboral") {
            $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
            $query_rundos = mysqli_query($con, $querydos);
            header("Location: maquinados.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al finalizar la jornada y pausar el maquinado";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Error al guardar la estadística, finalizar la jornada y pausar el maquinado";
        header("Location: inicioactividades.php?id=$id");
        exit(0);
    }
}

if (isset($_POST['lunchEnd'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        if ($query_rundos) {
            $query_verificar = "SELECT * FROM asistencia WHERE idcodigo='$idcodigo' AND salida IS NULL";
            $resultado_verificar = mysqli_query($con, $query_verificar);
            if (mysqli_num_rows($resultado_verificar) > 0) {
                // Si existe una fila, actualiza la salida
                $fila = mysqli_fetch_assoc($resultado_verificar);
                $id_asistencia = $fila['id']; // Suponiendo que el ID de la asistencia se llama 'id'
                $query_actualizar = "UPDATE asistencia SET salida='$hora_actual' WHERE id='$id_asistencia'";
                mysqli_query($con, $query_actualizar);
                $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Concluyo su jornada, maquinado en pausa, motivo: Lunch' WHERE `usuarios`.`codigo` = '$idcodigo'";
                $queryubicacion_run = mysqli_query($con, $queryubicacion);
                $_SESSION['message'] = "Concluyo su turno exitosamente y el maquinado se pauso por motivo: Lunch";
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividades.php?id=$id");
                exit();
            } else {
                $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Maquinado en pausa, error al registrar su salida para lunch' WHERE `usuarios`.`codigo` = '$idcodigo'";
                $queryubicacion_run = mysqli_query($con, $queryubicacion);
                $message = "Error: Se pauso el maquinado pero no se pudo registrar la salida de " . $nombre . ' ' . $apellidop . ', ' . "correctamente, notifique a RRHH, hora de salida: " . $hora_actual;
                $_SESSION['message'] = $message;
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividades.php?id=$id");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Error al pausar el maquinado, contacte a soporte";
        header("Location: inicioactividades.php?id=$id");
        exit(0);
    }
}

if (isset($_POST['saveensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialensamble SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ensamble en pausa, motivo: $motivosparo' WHERE `usuarios`.`codigo` = '$idcodigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
        $_SESSION['message'] = "Se detuvo la actividad de ensamble exitosamente, motivo $motivosparo";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar el paro, contacte a soporte";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    }
}

if (isset($_POST['startensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialensamble SET idcodigo='$idcodigo',idplano='$id', motivoactividad='Inicio', hora='$hora_actual', fecha='$fecha_actual'";
    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);
        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ensamble en progreso' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Iniciaste el ensamble exitosamente";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el ensamble, contacte a soporte";
            header("Location: ensamble.php");
            exit(0);
        }
    }
}

if (isset($_POST['restartensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $paro = $_SESSION['paro'];

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialensamble WHERE idcodigo = $idcodigo AND idplano = $id";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialensamble SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run && $paro == 'Lunch') {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $querytres = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_reingreso = mysqli_query($con, $querytres);
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un ensamble' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Reiniciaste el ensamble exitosamente, tu reingreso es a las " . $hora_actual;
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un ensamble, error al iniciar la jornada laboral' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Se reinicio el ensamble pero no su jornada laboral, contacte a soporte";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        }
    } else if ($query_run && $paro != 'Lunch') {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un ensamble' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Ensamble reiniciado exitosamente";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el ensamble y actualizar el historial, contacte a soporte";
            header("Location: ensamble.php");
            exit(0);
        }
    } else {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Reinicio un ensamble, error al guardar la estadística' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Ensamble reiniciado exitosamente, error al actualizar el historial.";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el ensamble y actualizar el historial, contacte a soporte";
            header("Location: ensamble.php");
            exit(0);
        }
    }
}

if (isset($_POST['finishensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialensamble WHERE idcodigo = $idcodigo AND idplano = $id AND motivoactividad = 'Inicio'";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialensamble SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '0' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);
        if ($querydos_run) {
            $_SESSION['message'] = "Terminaste el ensamble exitosamente";
            header("Location: ensamble.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al terminar el ensamble, contacte a soporte";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        }
    }
}

if (isset($_POST['pausarensamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialensamble SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        if ($motivosparo == "Fin de jornada laboral") {
            $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
            $query_rundos = mysqli_query($con, $querydos);
            header("Location: logout.php");
            exit(0);
        } else {
            $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
            $query_rundos = mysqli_query($con, $querydos);
            $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ensamble en pausa, motivo: $motivosparo' WHERE `usuarios`.`codigo` = '$idcodigo'";
            $queryubicacion_run = mysqli_query($con, $queryubicacion);
            $_SESSION['message'] = "Se pauso el ensamble exitosamente, motivo $motivosparo";
            $_SESSION['paro'] = $motivosparo;
            header("Location: ensamble.php");
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Error al pausar el ensamble, contacte a soporte";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    }
}

if (isset($_POST['lunchEndEnsamble'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");
    $query = "INSERT INTO historialensamble SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";
    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        if ($query_rundos) {
            $query_verificar = "SELECT * FROM asistencia WHERE idcodigo='$idcodigo' AND salida IS NULL";
            $resultado_verificar = mysqli_query($con, $query_verificar);
            if (mysqli_num_rows($resultado_verificar) > 0) {
                // Si existe una fila, actualiza la salida
                $fila = mysqli_fetch_assoc($resultado_verificar);
                $id_asistencia = $fila['id']; // Suponiendo que el ID de la asistencia se llama 'id'
                $query_actualizar = "UPDATE asistencia SET salida='$hora_actual' WHERE id='$id_asistencia'";
                mysqli_query($con, $query_actualizar);
                $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Concluyo su jornada, ensamble en pausa, motivo: Lunch' WHERE `usuarios`.`codigo` = '$idcodigo'";
                $queryubicacion_run = mysqli_query($con, $queryubicacion);
                $_SESSION['message'] = "Concluyo su turno exitosamente y el ensamble se pauso por motivo: Lunch";
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividadesensamble.php?id=$id");
                exit();
            } else {
                $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ensamble en pausa por lunch, error al registrar la salida' WHERE `usuarios`.`codigo` = '$idcodigo'";
                $queryubicacion_run = mysqli_query($con, $queryubicacion);
                $message = "Error: Se pauso el ensamble pero no se pudo registrar la salida de " . $nombre . ' ' . $apellidop . ', ' . "correctamente, notifique a RRHH, hora de salida: " . $hora_actual;
                $_SESSION['message'] = $message;
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividadesensamble.php?id=$id");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Error al pausar el ensamble, contacte a soporte";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    }
}
