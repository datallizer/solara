<?php
require 'dbcon.php';
session_start();
if (isset($_POST['save'])) {
    $idcodigo = $_SESSION['codigo'];
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $motivosparo = mysqli_real_escape_string($con, $_POST['motivosparo']);
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "INSERT INTO historialoperadores SET idcodigo='$idcodigo',idplano='$id', motivoactividad='$motivosparo', hora='$hora_actual', fecha='$fecha_actual'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '$motivosparo' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
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
    $paro = $_SESSION['paro'];

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialoperadores WHERE idcodigo = $idcodigo AND idplano = $id";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialoperadores SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run && $paro == 'Lunch') {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $querytres = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_reingreso = mysqli_query($con, $querytres);
            $_SESSION['message'] = "Reiniciaste el maquinado exitosamente, tu reingreso es a las " . $hora_actual;
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Se reinicio el maquinado pero no su jornada laboral, contacte a soporte";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        }
    } else if ($query_run && $paro != 'Lunch') {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
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

        if ($querydos_run) {
            $_SESSION['message'] = "Maquinado terminado exitosamente";
            header("Location: maquinados.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al finalizar el maquinado, contacte a soporte";
            header("Location: maquinados.php");
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
        } else {
            $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
            $query_rundos = mysqli_query($con, $querydos);
            $_SESSION['message'] = "Se pauso el maquinado exitosamente, motivo $motivosparo";
            $_SESSION['paro'] = $motivosparo;
            header("Location: maquinados.php");
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Error al pausar el maquinado, contacte a soporte";
        header("Location: maquinados.php");
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
                $_SESSION['message'] = "Concluyo su turno exitosamente y el maquinado se pauso por motivo: Lunch";
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividades.php?id=$id");
                exit();
            } else {
                $message = "Error: Se pauso el maquinado pero no se pudo registrar la salida de " . $nombre . ' ' . $apellidop . ', ' . "correctamente, notifique a RRHH, hora de salida: " . $hora_actual;
                $_SESSION['message'] = $message;
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividades.php?id=$id");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Error al pausar el maquinado, contacte a soporte";
        header("Location: maquinados.php");
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
        $_SESSION['message'] = "Se detuvo la actividad de ensamble exitosamente, motivo $motivosparo";
        header("Location: inicioactividadesensamble.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar el paro, contacte a soporte";
        header("Location: ensamble.php");
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
            $_SESSION['message'] = "Reiniciaste el ensamble exitosamente, tu reingreso es a las " . $hora_actual;
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Se reinicio el ensamble pero no su jornada laboral, contacte a soporte";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        }
    } else if ($query_run && $paro != 'Lunch') {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
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
            header("Location: ensamble.php");
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
            $_SESSION['message'] = "Se pauso el ensamble exitosamente, motivo $motivosparo";
            $_SESSION['paro'] = $motivosparo;
            header("Location: ensamble.php");
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Error al pausar el ensamble, contacte a soporte";
        header("Location: ensamble.php");
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
                $_SESSION['message'] = "Concluyo su turno exitosamente y el ensamble se pauso por motivo: Lunch";
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividadesensamble.php?id=$id");
                exit();
            } else {
                $message = "Error: Se pauso el ensamble pero no se pudo registrar la salida de " . $nombre . ' ' . $apellidop . ', ' . "correctamente, notifique a RRHH, hora de salida: " . $hora_actual;
                $_SESSION['message'] = $message;
                $_SESSION['paro'] = $motivosparo;
                header("Location: inicioactividadesensamble.php?id=$id");
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Error al pausar el ensamble, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}