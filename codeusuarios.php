<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Usuario eliminado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el usuario, contácte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

    // Directorio donde se guardarán las imágenes
    $upload_dir = './usuarios/';
    $file_path = $upload_dir . $id . '.jpg';

    // Verificar si se ha subido una nueva imagen
    if ($_FILES['nuevaFoto']['size'] > 0) {

        if (file_exists($file_path)) {
            unlink($file_path); // Elimina la imagen anterior si existe
        }

        // Obtener información de la imagen
        $image_tmp_name = $_FILES['nuevaFoto']['tmp_name'];
        $image_info = getimagesize($image_tmp_name);
        $image_type = $image_info[2];

        // Convertir la imagen a formato jpg
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $src_image = imagecreatefromjpeg($image_tmp_name);
                break;
            case IMAGETYPE_PNG:
                $src_image = imagecreatefrompng($image_tmp_name);
                break;
            case IMAGETYPE_GIF:
                $src_image = imagecreatefromgif($image_tmp_name);
                break;
            default:
                $_SESSION['message'] = "Formato de imagen no soportado";
                header("Location: usuarios.php");
                exit(0);
        }

        // Guardar la imagen convertida como .jpg
        imagejpeg($src_image, $file_path);
        imagedestroy($src_image);

        // Actualizar la base de datos con la ruta de la imagen
        $medio = mysqli_real_escape_string($con, $file_path);
        $update_query = "UPDATE usuarios SET medio='$medio' WHERE id='$id'";
        $update_result = mysqli_query($con, $update_query);

        if (!$update_result) {
            $_SESSION['message'] = "Error al actualizar la imagen del usuario, contácte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    }

    // Actualizar los datos del usuario
    $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `codigo` = '$codigo', `rol` = '$rol', `estatus` = '$estatus' WHERE `usuarios`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Usuario editado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}


if (isset($_POST['nominaupdate'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nomina = mysqli_real_escape_string($con, $_POST['nomina']);
    // Obtener la nueva imagen cargada
    $query = "UPDATE `usuarios` SET `nomina` = '$nomina' WHERE `usuarios`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito la nómina, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Nómina editado exitosamente";
        header("Location: nomina.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar la nómina, contacte a soporte";
        header("Location: nomina.php");
        exit(0);
    }
}


if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    // Verificar si el código ya existe en la tabla
    $query_check = "SELECT * FROM usuarios WHERE codigo='$codigo'";
    $result_check = mysqli_query($con, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Si el código ya existe
        $_SESSION['message'] = "Este código ya está en uso";
        header("Location: usuarios.php");
        exit(0);
    } else {
        // Preparar los datos para la inserción
        $query = "INSERT INTO usuarios (nombre, apellidop, apellidom, codigo, estatus, rol) VALUES ('$nombre', '$apellidop', '$apellidom', '$codigo', '1', '$rol')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $id = mysqli_insert_id($con); // Obtener el ID del nuevo registro

            // Inicializar la ruta de la imagen
            $ruta_imagen = '';

            // Procesar la imagen si se ha subido
            if (isset($_FILES['medio']) && $_FILES['medio']['error'] == 0) {
                $imagen_tmp = $_FILES['medio']['tmp_name'];
                $imagen_ext = strtolower(pathinfo($_FILES['medio']['name'], PATHINFO_EXTENSION));
                $imagen_nombre = $id . '.jpg'; // Nombre del archivo con extensión .jpg
                $imagen_destino = 'usuarios/' . $imagen_nombre;

                // Convertir a JPG
                $imagen = imagecreatefromstring(file_get_contents($imagen_tmp));
                if ($imagen) {
                    imagejpeg($imagen, $imagen_destino, 100); // Guardar como JPG con calidad 100
                    imagedestroy($imagen);

                    // Establecer la ruta de la imagen
                    $ruta_imagen = $imagen_destino;
                }
            }

            // Actualizar la base de datos con la ruta de la imagen
            $query_update = "UPDATE usuarios SET medio='./$ruta_imagen' WHERE id='$id'";
            $query_update_run = mysqli_query($con, $query_update);

            $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
            $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

            $querydos = "INSERT INTO historial (idcodigo, detalles, hora, fecha) VALUES ('$id', 'Registro un nuevo usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol', '$hora_actual', '$fecha_actual')";
            $query_rundos = mysqli_query($con, $querydos);

            $_SESSION['message'] = "Usuario creado exitosamente";
            header("Location: usuarios.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al crear el usuario, contacte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    }
}

if (isset($_POST['emailsave'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $idcodigo = $_SESSION['codigo']; // Recuperar el código guardado en la sesión

    // Actualizar el email del usuario en la base de datos
    $query = "UPDATE usuarios SET email='$email' WHERE codigo='$idcodigo'";
    $result = mysqli_query($con, $query);

    if ($result) {
        $_SESSION['email'] = $email;
        $_SESSION['message'] = "Email guardado correctamente.";
        header("Location: dashboard.php"); // Redirigir después de guardar el email
        exit();
    } else {
        $_SESSION['message'] = "Email no guardado, contacte a soporte.";
        header("Location: logout.php"); // Redirigir después de guardar el email
        exit();
    }
}
