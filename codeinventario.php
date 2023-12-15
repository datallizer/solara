<?php
require 'dbcon.php';
session_start();

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM inventario WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Material eliminado exitosamente";
        header("Location: inventario.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el material, contacte a soporte";
        header("Location: inventario.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $clasificacion = mysqli_real_escape_string($con, $_POST['clasificacion']);
    $tipo = mysqli_real_escape_string($con, $_POST['tipo']);
    $proveedor = mysqli_real_escape_string($con, $_POST['proveedor']);
    $parte = mysqli_real_escape_string($con, $_POST['parte']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $marca = mysqli_real_escape_string($con, $_POST['marca']);
    $condicion = mysqli_real_escape_string($con, $_POST['condicion']);
    $cantidad = mysqli_real_escape_string($con, $_POST['cantidad']);
    $rack = mysqli_real_escape_string($con, $_POST['rack']);
    $bin = mysqli_real_escape_string($con, $_POST['bin']);
    $caja = mysqli_real_escape_string($con, $_POST['caja']);
    $costo = mysqli_real_escape_string($con, $_POST['costo']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $numero = mysqli_real_escape_string($con, $_POST['numero']);

    $query = "UPDATE `inventario` SET `clasificacion` = '$clasificacion', `tipo` = '$tipo', `proveedor` = '$proveedor', `parte` = '$parte', `descripcion` = '$descripcion', `marca` = '$marca', `condicion` = '$condicion', `cantidad` = '$cantidad', `rack` = '$rack', `bin` = '$bin', `caja` = '$caja', `costo` = '$costo', `nombre` = '$nombre', `numero` = '$numero' WHERE `inventario`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Material editado exitosamente";
        header("Location: inventario.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el material, contacte a soporte";
        header("Location: inventario.php");
        exit(0);
    }
}

if (isset($_POST['sumar'])) {
    $id = $_POST['id'];
    $entrada = $_POST['entrada'];
    
    // Consultar la cantidad actual y el nombre del inventario
    $query_actual = "SELECT cantidad, nombre FROM inventario WHERE id = $id";
    $result_actual = mysqli_query($con, $query_actual);

    if ($result_actual && mysqli_num_rows($result_actual) > 0) {
        $row = mysqli_fetch_assoc($result_actual);
        $cantidad_actual = $row['cantidad'];
        $nombre = $row['nombre'];

        // Realizar la resta y verificar si la cantidad es válida
        $cantidad = $cantidad_actual + $entrada;

        if ($cantidad >= 0) {
            $query_update = "UPDATE inventario SET cantidad = '$cantidad' WHERE id = '$id'";
            $query_run = mysqli_query($con, $query_update);

            if ($query_run) {
                $idcodigo = $_SESSION['codigo'];
                $fecha_actual = date("Y-m-d");
                $hora_actual = date("H:i");
                $query_historial = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Añadiste del inventario $entrada piezas de $nombre', hora='$hora_actual', fecha='$fecha_actual'";
                $query_run_historial = mysqli_query($con, $query_historial);

                $_SESSION['message'] = "Añadiste al inventario $entrada piezas de $nombre";
                header("Location: inventario.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al agregar al inventario, contacta a soporte";
                header("Location: inventario.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "No puedes añadir esta cantidad porque supera el stock permitido";
            header("Location: inventario.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Inventario no encontrado";
        header("Location: inventario.php");
        exit();
    }
}

if (isset($_POST['restar'])) {
    $id = $_POST['id'];
    $salida = $_POST['salida'];
    
    // Consultar la cantidad actual y el nombre del inventario
    $query_actual = "SELECT cantidad, nombre FROM inventario WHERE id = $id";
    $result_actual = mysqli_query($con, $query_actual);

    if ($result_actual && mysqli_num_rows($result_actual) > 0) {
        $row = mysqli_fetch_assoc($result_actual);
        $cantidad_actual = $row['cantidad'];
        $nombre = $row['nombre'];

        // Realizar la resta y verificar si la cantidad es válida
        $cantidad = $cantidad_actual - $salida;

        if ($cantidad >= 0) {
            $query_update = "UPDATE inventario SET cantidad = '$cantidad' WHERE id = '$id'";
            $query_run = mysqli_query($con, $query_update);

            if ($query_run) {
                $idcodigo = $_SESSION['codigo'];
                $fecha_actual = date("Y-m-d");
                $hora_actual = date("H:i");
                $query_historial = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Retiro del inventario $salida piezas de $nombre', hora='$hora_actual', fecha='$fecha_actual'";
                $query_run_historial = mysqli_query($con, $query_historial);

                $_SESSION['message'] = "Retiraste del inventario $salida piezas de $nombre";
                header("Location: inventario.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al editar el inventario, contacta a soporte";
                header("Location: inventario.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "No puedes sacar esta cantidad porque supera el stock actual";
            header("Location: inventario.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Inventario no encontrado";
        header("Location: inventario.php");
        exit();
    }
}


if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $cantidad = mysqli_real_escape_string($con, $_POST['cantidad']);
    $ubicacion = mysqli_real_escape_string($con, $_POST['ubicacion']);
    $clasificacion = mysqli_real_escape_string($con, $_POST['clasificacion']);
    $tipo = mysqli_real_escape_string($con, $_POST['tipo']);
    $proveedor = mysqli_real_escape_string($con, $_POST['proveedor']);
    $parte = mysqli_real_escape_string($con, $_POST['parte']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $marca = mysqli_real_escape_string($con, $_POST['marca']);
    $condicion = mysqli_real_escape_string($con, $_POST['condicion']);
    $rack = mysqli_real_escape_string($con, $_POST['rack']);
    $bin = mysqli_real_escape_string($con, $_POST['bin']);
    $caja = mysqli_real_escape_string($con, $_POST['caja']);
    $numero = mysqli_real_escape_string($con, $_POST['numero']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);

    $query = "INSERT INTO inventario SET nombre='$nombre', cantidad='$cantidad', ubicacion='$ubicacion', clasificacion='$clasificacion', tipo='$tipo', proveedor='$proveedor',parte='$parte',descripcion='$descripcion',marca='$marca',condicion='$condicion',rack='$rack',bin='$bin',caja='$caja',numero='$numero'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo material, nombre: $nombre, cantidad: $cantidad', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Material registrado exitosamente";
        header("Location: inventario.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar el material, contacte a soporte";
        header("Location: inventario.php");
        exit(0);
    }
}
