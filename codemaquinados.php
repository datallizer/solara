<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM plano WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    $queryAsignacion = "DELETE FROM asignacionplano WHERE idplano='$registro_id' ";
    $query_run = mysqli_query($con, $queryAsignacion);

    $queryHistorial = "DELETE FROM historialoperadores WHERE idplano='$registro_id' ";
    $query_run = mysqli_query($con, $queryHistorial);

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

    // Verifica si se ha subido un archivo
    if (isset($_FILES['medio']) && $_FILES['medio']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['medio']['tmp_name'];
        $file_name = $id . '.pdf'; // El nombre del archivo será el ID seguido de la extensión .pdf
        $file_dest = 'planos/' . $file_name;

        // Mueve el archivo a la carpeta 'planos'
        if (move_uploaded_file($file_tmp, $file_dest)) {
            $query .= ", `medio` = './planos/$file_name'"; // Solo almacena el nombre del archivo en la base de datos
        } else {
            $_SESSION['message'] = "Error al mover el archivo. Intente de nuevo.";
            header("Location: maquinados.php");
            exit(0);
        }
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
    // Escapar otros valores POST no array
    $idproyecto = isset($_POST['idproyecto']) ? mysqli_real_escape_string($con, $_POST['idproyecto']) : '';
    $nombreplano = isset($_POST['nombreplano']) ? mysqli_real_escape_string($con, $_POST['nombreplano']) : '';
    $nivel = isset($_POST['nivel']) ? mysqli_real_escape_string($con, $_POST['nivel']) : '';
    $piezas = isset($_POST['piezas']) ? mysqli_real_escape_string($con, $_POST['piezas']) : '';
    $actividad = isset($_POST['actividad']) ? mysqli_real_escape_string($con, $_POST['actividad']) : '';

    // Comprobar si se ha subido un archivo
    if (isset($_FILES['medio']) && $_FILES['medio']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['medio']['tmp_name'];
        $fileName = $_FILES['medio']['name'];
        $fileSize = $_FILES['medio']['size'];
        $fileType = $_FILES['medio']['type'];

        // Verificar que el archivo sea un PDF
        if ($fileType === 'application/pdf') {
            // Aquí, en lugar de almacenar el archivo, primero insertamos el registro para obtener el ID
            $query = "INSERT INTO plano (idproyecto, nombreplano, medio, nivel, piezas, actividad, estatusplano) VALUES (?, ?, ?, ?, ?, ?, '1')";
            $stmt = mysqli_prepare($con, $query);

            // Medio solo será la referencia del archivo, inicialmente vacío
            $medio = ''; // Lo actualizaremos después

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssis', $idproyecto, $nombreplano, $medio, $nivel, $piezas, $actividad);
                mysqli_stmt_execute($stmt);

                // Obtener el ID del registro recién creado
                $idplano = mysqli_insert_id($con);

                // Crear la ruta donde se guardará el archivo
                $uploadFileDir = './planos/';
                $dest_path = $uploadFileDir . $idplano . '.pdf';

                // Mover el archivo a la carpeta "planos" con el ID del registro como nombre
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Actualizar el campo `medio` con la ruta del archivo
                    $queryUpdate = "UPDATE plano SET medio = ? WHERE id = ?";
                    $stmtUpdate = mysqli_prepare($con, $queryUpdate);

                    if ($stmtUpdate) {
                        mysqli_stmt_bind_param($stmtUpdate, 'si', $dest_path, $idplano);
                        mysqli_stmt_execute($stmtUpdate);
                    }

                    // Procesar otros valores (por ejemplo, asignar a operadores)
                    if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
                        foreach ($_POST['codigooperador'] as $codigoOperador) {
                            $queryplano = "INSERT INTO asignacionplano (idplano, codigooperador) VALUES (?, ?)";
                            $stmtPlano = mysqli_prepare($con, $queryplano);

                            if ($stmtPlano) {
                                mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                                mysqli_stmt_execute($stmtPlano);

                                // Código para notificaciones, etc.
                                $idcodigo = $_SESSION['codigo'];
                                $fecha_actual = date("Y-m-d");
                                $hora_actual = date("H:i");

                                $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo maquinado: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                                mysqli_query($con, $querydos);

                                $mensaje = 'Tienes un nuevo maquinado, Nombre: ' . $nombreplano . ' Actividad: ' . $actividad  . ' Prioridad: ' . $nivel;
                                $emisor = $_SESSION['codigo'];
                                $estatus = '1';

                                $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$codigoOperador', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                                mysqli_query($con, $querymensajes);
                            }
                        }
                    }

                    $_SESSION['message'] = "Maquinado creado exitosamente";
                    header("Location: maquinados.php");
                    exit(0);
                } else {
                    $_SESSION['message'] = "Error al mover el archivo PDF.";
                    header("Location: maquinados.php");
                    exit(0);
                }
            } else {
                $_SESSION['message'] = "Error al crear el maquinado.";
                header("Location: maquinados.php");
                exit(0);
            }
        } else {
            $_SESSION['message'] = "Por favor, sube un archivo PDF válido.";
            header("Location: maquinados.php");
            exit(0);
        }
    } else {
        // Aquí, en lugar de almacenar el archivo, primero insertamos el registro para obtener el ID
        $query = "INSERT INTO plano (idproyecto, nombreplano, nivel, piezas, actividad, estatusplano) VALUES (?, ?, ?, ?, ?, '1')";
        $stmt = mysqli_prepare($con, $query);


        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssis', $idproyecto, $nombreplano, $nivel, $piezas, $actividad);
            mysqli_stmt_execute($stmt);

            // Obtener el ID del registro recién creado
            $idplano = mysqli_insert_id($con);

            // Procesar otros valores (por ejemplo, asignar a operadores)
            if (!empty($_POST['codigooperador']) && is_array($_POST['codigooperador'])) {
                foreach ($_POST['codigooperador'] as $codigoOperador) {
                    $queryplano = "INSERT INTO asignacionplano (idplano, codigooperador) VALUES (?, ?)";
                    $stmtPlano = mysqli_prepare($con, $queryplano);

                    if ($stmtPlano) {
                        mysqli_stmt_bind_param($stmtPlano, 'ii', $idplano, $codigoOperador);
                        mysqli_stmt_execute($stmtPlano);

                        // Código para notificaciones, etc.
                        $idcodigo = $_SESSION['codigo'];
                        $fecha_actual = date("Y-m-d");
                        $hora_actual = date("H:i");

                        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Subio un nuevo maquinado: $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                        mysqli_query($con, $querydos);

                        $mensaje = 'Tienes un nuevo maquinado, Nombre: ' . $nombreplano . ' Actividad: ' . $actividad  . ' Prioridad: ' . $nivel;
                        $emisor = $_SESSION['codigo'];
                        $estatus = '1';

                        $querymensajes = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES ('$mensaje', '$codigoOperador', '$emisor', '$fecha_actual', '$hora_actual', '$estatus')";
                        mysqli_query($con, $querymensajes);
                    }
                }
            }
            $_SESSION['message'] = "Maquinado creado exitosamente";
            header("Location: maquinados.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear el maquinado.";
            header("Location: maquinados.php");
            exit(0);
        }
    }
}
