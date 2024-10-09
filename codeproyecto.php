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
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);

    $query = "UPDATE `proyecto` SET `nombre` = '$nombre', `cliente` = '$cliente', `presupuesto` = '$presupuesto', `fechainicio` = '$fechainicio', `fechafin` = '$fechafin', `estatus` = '$estatus', `prioridad` = '$prioridad', `etapa` = '$etapa',`detalles` = '$detalles' WHERE `proyecto`.`id` = '$id'";
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

if (isset($_POST['aprobar'])) {
    $id = mysqli_real_escape_string($con, $_POST['aprobar']);
    $estatus = 1;
    $etapa = 6;

    $query = "UPDATE `proyecto` SET `estatus` = '$estatus', `etapa` = '$etapa' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Proyecto aprobado exitosamente";
        header("Location: editarproyecto.php?id=$id");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el proyecto, contácte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

if (isset($_POST['archivaranteproyecto'])) {
    $id = mysqli_real_escape_string($con, $_POST['archivaranteproyecto']);
    $estatus = 3;

    $query = "UPDATE `proyecto` SET `estatus` = '$estatus' WHERE `proyecto`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Proyecto editado exitosamente";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el proyecto, contácte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

if (isset($_POST['etapas'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);

    $query = "UPDATE `proyecto` SET `etapa` = '$etapa' WHERE `proyecto`.`id` = '$id'";
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
    // $etapadiseño = mysqli_real_escape_string($con, $_POST['etapadiseño']);
    // $etapacontrol = mysqli_real_escape_string($con, $_POST['etapacontrol']);
    // $etapatcontrol = mysqli_real_escape_string($con, $_POST['etapatcontrol']);
    // $etapamecanica = mysqli_real_escape_string($con, $_POST['etapamecanica']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $estatus = '1';
    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
        // Insertar el registro en la tabla `plano` una sola vez fuera del bucle
        // $query = "INSERT INTO proyecto SET nombre='$nombre', cliente='$cliente', prioridad='$prioridad', fechainicio='$fechainicio', fechafin='$fechafin', detalles='$detalles', presupuesto='$presupuesto', estatus='1',etapadiseño='$etapadiseño',etapacontrol='$etapacontrol'";

        $query = "INSERT INTO proyecto (nombre, cliente, prioridad, fechainicio, fechafin, detalles, presupuesto, estatus, etapa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssi', $nombre, $cliente, $prioridad, $fechainicio, $fechafin, $detalles, $presupuesto, $estatus, $etapa);
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

if (isset($_POST['anteproyecto'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $cliente = mysqli_real_escape_string($con, $_POST['cliente']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $estatus = '2';
    // Verify if checkboxes are selected and process each value
    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {

        $query = "INSERT INTO proyecto (nombre, cliente, estatus, etapa) VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssi', $nombre, $cliente, $estatus, $etapa);
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
                    $mensaje = 'Tienes un nuevo anteproyecto asignado, Nombre: ' . $nombre . ' Cliente: ' . $cliente;

                    $idcodigo = $codigoOperador;

                    $emisor = $_SESSION['codigo'];
                    $estatus = '1';

                    $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$idcodigo', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                    $querymensajes_run = mysqli_query($con, $querymensajes);
                } else {
                    $_SESSION['message'] = "Error al crear el anteproyecto, contacte a soporte";
                    header("Location: anteproyectos.php");
                    exit(0);
                }
            }
            $_SESSION['message'] = "Anteproyecto creado exitosamente";
            header("Location: anteproyectos.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear el anteproyecto, contacte a soporte";
            header("Location: anteproyectos.php");
            exit(0);
        }
    }
}

if (isset($_POST['agendalevantamiento'])) {
    $idproyecto = mysqli_real_escape_string($con, $_POST['idproyecto']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $dia = mysqli_real_escape_string($con, $_POST['dia']);
    $hora = mysqli_real_escape_string($con, $_POST['hora']);
    $estatus = 1;
    $idcodigo = $_SESSION['codigo'];
    $query = "INSERT INTO agendaproyectos SET idproyecto='$idproyecto', etapa='$etapa', dia='$dia', hora='$hora', estatus='$estatus', idcodigo='$idcodigo'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Visita agendada correctamente";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar la visita, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Configuracion SMTP
$host = 'smtp.gmail.com';
$port = 587;
$username = 'solarasystemai@gmail.com';
$password = 'owwd pbtr bpfh brff';
$security = 'tls';

if (isset($_POST['minuta'])) {
    $detalles = nl2br($_POST['detalles']);
    $asunto = mysqli_real_escape_string($con, $_POST['asunto']);
    $idcodigoagenda = mysqli_real_escape_string($con, $_POST['idcodigoagenda']);
    $idregistroagenda = mysqli_real_escape_string($con, $_POST['idregistroagenda']);
    $idproyectoagenda = mysqli_real_escape_string($con, $_POST['idproyectoagenda']);

    $nombreuser = $_SESSION['nombre'] . ' ' . $_SESSION['apellidop'];
    $emailpuser = $_SESSION['email'];

    // Crear instancia PHPMailer
    $mail = new PHPMailer(true);


    // Configurar SMTP
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = $security;
    //$mail->SMTPDebug = 2;

    // Configurar correo
    $mail->setFrom('solarasystemai@gmail.com', $nombreuser);
    $mail->addReplyTo($emailpuser, $nombreuser);
    $mail->addAddress('m.prado@solara-industries.com');
    $mail->addAddress('m.villa@solara-industries.com');
    $mail->Subject = $asunto;
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    // Cuerpo del mensaje
    $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body>
            <img style="width:100%;" src="https://datallizer.com/images/solarasuperior.jpg" alt="">
            <h1 style="font-size:25px;margin-top:30px;text-align:left;margin-bottom:30px;"><b>Minuta</b></h1>
            <pre style="font-size:12px;">' . $detalles . '</pre>

            <p style="font-size:8px;">Este es un email enviado desde el canal de comunicación de Google del sistema de planificación de recursos empresariales SOLARA AI, siendo intermediario solarsystemai@gmail.com y con respuesta directa al email personal del emisor, la información previa a sido generada utilizando datos almacenados en la base de datos de SOLARA y el detalle manipulado manualmente por el usuario autor, es importante tener en cuenta que los detalles presentados podrían estar desactualizados o contener errores. Le recomendamos verificar la precisión de la información presentada antes de tomar decisiones basadas en estos datos.</p>
            </body>
            </html>
            ';
    $mail->Body = $body;

    // Enviar correo
    if ($mail->send()) {
        $query = "UPDATE agendaproyectos SET estatus = 0 WHERE id = $idregistroagenda";
        $querydos = "UPDATE proyecto SET etapa = 3 WHERE id = $idproyectoagenda";
        mysqli_query($con, $querydos);

        if (mysqli_query($con, $query)) {
            // Se actualizó correctamente la base de datos
            $_SESSION['message'] = "Correo enviado y etapa actualizada.";
        } else {
            // Hubo un error al actualizar la base de datos
            $_SESSION['message'] = "Correo enviado, pero no se pudo actualizar la etapa: " . mysqli_error($con);
        }
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Correo no enviado, notifique a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}


if (isset($_POST['documento'])) {
    $idproyecto = mysqli_real_escape_string($con, $_POST['idproyecto']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $estatus = 1;
    $idcodigo = $_SESSION['codigo'];

    if ($_SESSION['rol'] == 5) {
        $tipo = 'Diseño';
    } elseif ($_SESSION['rol'] == 9) {
        $tipo = 'Diagrama';
    }

    $query = "INSERT INTO proyectomedios SET idproyecto='$idproyecto', etapa='$etapa', estatus='$estatus', idcodigo='$idcodigo', tipo='$tipo'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $pdf_id = mysqli_insert_id($con);

        if (isset($_FILES['medio']) && $_FILES['medio']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_name = $_FILES['medio']['tmp_name'];
            $file_name = $tipo . $pdf_id . '.pdf';

            if ($_SESSION['rol'] == 5) {
                $upload_dir = './disenoBloques/';
            } elseif ($_SESSION['rol'] == 9) {
                $upload_dir = './diagramaBloques/';
            }

            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp_name, $file_path)) {
                $update_query = "UPDATE proyectomedios SET medio='$file_path' WHERE id='$pdf_id'";
                mysqli_query($con, $update_query);
                $_SESSION['message'] = "Archivo subido y datos enviados exitosamente";
            } else {
                $_SESSION['message'] = "Error al subir el archivo PDF, contacte a soporte";
            }
        } else {
            $_SESSION['message'] = "No se ha subido ningún archivo PDF";
        }
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el quote, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

if (isset($_POST['aprobarbloque'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $idproyecto = mysqli_real_escape_string($con, $_POST['idproyecto']);

    // Actualizar estatus en proyectomedios
    $query = "UPDATE `proyectomedios` SET `estatus` = '3' WHERE `proyectomedios`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        // Contar registros en proyectomedios con el mismo idproyecto
        $count_query = "SELECT COUNT(*) AS total FROM `proyectomedios` WHERE `idproyecto` = '$idproyecto' AND `estatus` = '3'";
        $count_result = mysqli_query($con, $count_query);
        $count_data = mysqli_fetch_assoc($count_result);

        // Si hay 2 o más registros, actualizar la etapa a 4 en la tabla proyecto
        if ($count_data['total'] >= 2) {
            $update_etapa_query = "UPDATE `proyecto` SET `etapa` = '4' WHERE `id` = '$idproyecto'";
            mysqli_query($con, $update_etapa_query);
        }

        $_SESSION['message'] = "Aprobado exitosamente";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}


if (isset($_POST['rechazarbloque'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);

    $query = "UPDATE `proyectomedios` SET `estatus` = '2', `detalles` = '$detalles' WHERE `proyectomedios`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Rechazado exitosamente, se notifico al ingeniero para su corrección";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

if (isset($_POST['bom'])) {
    $idproyecto = mysqli_real_escape_string($con, $_POST['idproyecto']);
    $etapa = mysqli_real_escape_string($con, $_POST['etapa']);
    $monto = mysqli_real_escape_string($con, $_POST['monto']);
    $estatus = 1;
    $idcodigo = $_SESSION['codigo'];

    if ($_SESSION['rol'] == 5) {
        $tipo = 'Diseño';
    } elseif ($_SESSION['rol'] == 9) {
        $tipo = 'Control';
    }

    $query = "INSERT INTO proyectoboms SET idproyecto='$idproyecto', etapa='$etapa', monto='$monto', estatus='$estatus', idcodigo='$idcodigo', tipo='$tipo'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $pdf_id = mysqli_insert_id($con);

        if (isset($_FILES['medio']) && $_FILES['medio']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_name = $_FILES['medio']['tmp_name'];
            $file_name = $tipo . $pdf_id . '.pdf';

            if ($_SESSION['rol'] == 5) {
                $upload_dir = './bomDiseño/';
            } elseif ($_SESSION['rol'] == 9) {
                $upload_dir = './bomControl/';
            }

            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp_name, $file_path)) {
                $update_query = "UPDATE proyectoboms SET medio='$file_path' WHERE id='$pdf_id'";
                mysqli_query($con, $update_query);
                $_SESSION['message'] = "Archivo subido y datos enviados exitosamente";
            } else {
                $_SESSION['message'] = "Error al subir el archivo PDF, contacte a soporte";
            }
        } else {
            $_SESSION['message'] = "No se ha subido ningún archivo PDF";
        }
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el quote, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}

if (isset($_POST['aprobarBom'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $idproyecto = mysqli_real_escape_string($con, $_POST['idproyecto']);

    // Actualizar estatus en proyectoboms
    $query = "UPDATE `proyectoboms` SET `estatus` = '3' WHERE `proyectoboms`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        // Contar registros en proyectoboms con el mismo idproyecto
        $count_query = "SELECT COUNT(*) AS total FROM `proyectoboms` WHERE `idproyecto` = '$idproyecto' AND `estatus` = '3'";
        $count_result = mysqli_query($con, $count_query);
        $count_data = mysqli_fetch_assoc($count_result);

        // Si hay 2 o más registros, actualizar la etapa a 4 en la tabla proyecto
        if ($count_data['total'] >= 2) {
            $update_etapa_query = "UPDATE `proyecto` SET `etapa` = '5' WHERE `id` = '$idproyecto'";
            mysqli_query($con, $update_etapa_query);
        }

        $_SESSION['message'] = "Aprobado exitosamente";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}


if (isset($_POST['rechazarBom'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $detalles = mysqli_real_escape_string($con, $_POST['detalles']);

    $query = "UPDATE `proyectoboms` SET `estatus` = '2', `detalles` = '$detalles' WHERE `proyectoboms`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Rechazado exitosamente, se notifico al ingeniero para su corrección";
        header("Location: anteproyectos.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al aprobar, contacte a soporte";
        header("Location: anteproyectos.php");
        exit(0);
    }
}