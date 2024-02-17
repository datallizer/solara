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

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialoperadores WHERE idcodigo = $idcodigo AND idplano = $id";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialoperadores SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $_SESSION['message'] = "Reiniciaste el maquinado exitosamente";
            header("Location: inicioactividades.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el maquinado, contacte a soporte";
            header("Location: maquinados.php");
            exit(0);
        }
    } else {
        $querydos = "UPDATE `plano` SET `estatusplano` = '3' WHERE `plano`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $_SESSION['message'] = "Error al actualizar la actividad";
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
        $querydos = "UPDATE `plano` SET `estatusplano` = '2' WHERE `plano`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Se pauso el maquinado exitosamente, motivo $motivosparo";
        header("Location: maquinados.php");
        exit(0);
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

    // Obtener el ID de la última fila que cumpla con la condición WHERE
    $subquery = "SELECT MAX(id) AS max_id FROM historialensamble WHERE idcodigo = $idcodigo AND idplano = $id";
    $result = mysqli_query($con, $subquery);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];

    // Actualizar solo la fila con el ID obtenido
    $query = "UPDATE historialensamble SET horareinicio = '$hora_actual', fechareinicio = '$fecha_actual' WHERE id = $max_id";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $_SESSION['message'] = "Reiniciaste actividades de ensamble exitosamente";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el paro, contacte a soporte";
            header("Location: ensamble.php");
            exit(0);
        }
    } else{
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '3' WHERE `diagrama`.`id` = '$id'";
        $querydos_run = mysqli_query($con, $querydos);

        if ($querydos_run) {
            $_SESSION['message'] = "Error al actualizar la actividad";
            header("Location: inicioactividadesensamble.php?id=$id");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al reiniciar el paro, contacte a soporte";
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
        $querydos = "UPDATE `diagrama` SET `estatusplano` = '2' WHERE `diagrama`.`id` = '$id'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Pausaste el ensamble, motivo: $motivosparo";
        header("Location: ensamble.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al terminar el ensamble, contacte a soporte";
        header("Location: ensamble.php");
        exit(0);
    }
}
