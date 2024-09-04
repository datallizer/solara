<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// if (isset($_POST['mecanico'])) {
    
//         $idplano = isset($_POST['idplano']) ? mysqli_real_escape_string($con, $_POST['idplano']) : '';
//             foreach ($_POST['codigooperador'] as $codigoOperador) {
//                 // Insertar en la tabla `asignaciondiagrama` utilizando el ID obtenido anteriormente
//                 $queryplano = "INSERT INTO asignaciondiagrama (idplano, codigooperador) VALUES (?, ?)";
//                 $stmtPlano = mysqli_prepare($con, $queryplano);

//                 if ($stmtPlano) {
//                     mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
//                     mysqli_stmt_execute($stmtPlano);
//                 } else {
//                     $_SESSION['message'] = "Error al asignar técnico(s) mecánico(s) al proyecto, contacte a soporte";
//                     header("Location: ensamble.php");
//                     exit(0);
//                 }
//             }
//             $_SESSION['message'] = "Técnico(s) mecánico(s) asignados al proyecto exitosamente";
//             header("Location: ensamble.php");
//             exit(0);
        
//     }

    if (isset($_POST['control'])) {
            $idplano = isset($_POST['idplano']) ? mysqli_real_escape_string($con, $_POST['idplano']) : '';
                foreach ($_POST['codigooperador'] as $codigoOperador) {
                    // Insertar en la tabla `asignaciondiagrama` utilizando el ID obtenido anteriormente
                    $queryplano = "INSERT INTO asignaciondiagrama (idplano, codigooperador) VALUES (?, ?)";
                    $stmtPlano = mysqli_prepare($con, $queryplano);
    
                    if ($stmtPlano) {
                        mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                        mysqli_stmt_execute($stmtPlano);

                        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                        $mensaje = 'Tienes un nuevo ensamble asignado';

                        $idcodigo = $codigoOperador;
    
                        $emisor = $_SESSION['codigo'];
                        $estatus = '1';
    
                        $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                        $querymensajes_run = mysqli_query($con, $querymensajes);
                    } else {
                        $_SESSION['message'] = "Error al asignar técnico en control al proyecto, contacte a soporte";
                        header("Location: ensamble.php");
                        exit(0);
                    }
                }
                $_SESSION['message'] = "Técnico(s) en control asignado(s) al proyecto exitosamente";
                header("Location: ensamble.php");
                exit(0);
        }

        if (isset($_POST['plano'])) {
            $idplano = isset($_POST['idplano']) ? mysqli_real_escape_string($con, $_POST['idplano']) : '';
                foreach ($_POST['codigooperador'] as $codigoOperador) {
                    // Insertar en la tabla `asignacionplano` utilizando el ID obtenido anteriormente
                    $queryplano = "INSERT INTO asignacionplano (idplano, codigooperador) VALUES (?, ?)";
                    $stmtPlano = mysqli_prepare($con, $queryplano);
    
                    if ($stmtPlano) {
                        mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                        mysqli_stmt_execute($stmtPlano);

                        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                        $mensaje = 'Tienes un nuevo maquinado asignado';

                        $idcodigo = $codigoOperador;
    
                        $emisor = $_SESSION['codigo'];
                        $estatus = '1';
    
                        $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                        $querymensajes_run = mysqli_query($con, $querymensajes);
                    } else {
                        $_SESSION['message'] = "Error al asignar operador(es) a maquinado, contacte a soporte";
                        header("Location: maquinados.php");
                        exit(0);
                    }
                }
                $_SESSION['message'] = "Operador(es) asignado(s) a maquinado exitosamente";
                header("Location: maquinados.php");
                exit(0);
        }

        if (isset($_POST['ingeniero'])) {
            $idplano = isset($_POST['idplano']) ? mysqli_real_escape_string($con, $_POST['idplano']) : '';
                foreach ($_POST['codigooperador'] as $codigoOperador) {
                    // Insertar en la tabla `asignacionplano` utilizando el ID obtenido anteriormente
                    $queryplano = "INSERT INTO asignacioningenieria (idplano, codigooperador) VALUES (?, ?)";
                    $stmtPlano = mysqli_prepare($con, $queryplano);
    
                    if ($stmtPlano) {
                        mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                        mysqli_stmt_execute($stmtPlano);

                        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                        $mensaje = 'Tienes una nueva actividad de ingeniería';

                        $idcodigo = $codigoOperador;
    
                        $emisor = $_SESSION['codigo'];
                        $estatus = '1';
    
                        $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                        $querymensajes_run = mysqli_query($con, $querymensajes);
                    } else {
                        $_SESSION['message'] = "Error al asignar la actividad, contacte a soporte";
                        header("Location: ingenieria.php");
                        exit(0);
                    }
                }
                $_SESSION['message'] = "Ingeniero(s) asignado(s) exitosamente";
                header("Location: ingenieria.php");
                exit(0);
        }

        if (isset($_POST['proyecto'])) {
            $idproyecto = isset($_POST['idproyecto']) ? mysqli_real_escape_string($con, $_POST['idproyecto']) : '';
                foreach ($_POST['codigooperador'] as $codigoOperador) {
                    // Insertar en la tabla `encargadotcontrol` utilizando el ID obtenido anteriormente
                    $queryplano = "INSERT INTO encargadoproyecto (idproyecto, codigooperador) VALUES (?, ?)";
                    $stmtPlano = mysqli_prepare($con, $queryplano);
    
                    if ($stmtPlano) {
                        mysqli_stmt_bind_param($stmtPlano, 'ii', $idproyecto, $codigoOperador);
                        mysqli_stmt_execute($stmtPlano);

                        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                        $mensaje = 'Tienes un nuevo proyecto asignado';

                        $idcodigo = $codigoOperador;
    
                        $emisor = $_SESSION['codigo'];
                        $estatus = '1';
    
                        $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                        $querymensajes_run = mysqli_query($con, $querymensajes);
                    } else {
                        $_SESSION['message'] = "Error al asignar encargado(s) de proyecto, contacte a soporte";
                        header("Location: proyectos.php");
                        exit(0);
                    }
                }
                $_SESSION['message'] = "Encargado(s) de proyecto asignados exitosamente";
                header("Location: proyectos.php");
                exit(0);
        }

?>