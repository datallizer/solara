<?php
require 'dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM bom WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "BOM eliminado exitosamente";
        header("Location: bom.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el BOM, contacte a soporte";
        header("Location: bom.php");
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
    $maximo = mysqli_real_escape_string($con, $_POST['maximo']);
    $minimo = mysqli_real_escape_string($con, $_POST['minimo']);
    

    $query = "UPDATE `bom` SET `clasificacion` = '$clasificacion', `tipo` = '$tipo', `proveedor` = '$proveedor', `parte` = '$parte', `descripcion` = '$descripcion', `marca` = '$marca', `condicion` = '$condicion', `cantidad` = '$cantidad', `rack` = '$rack', `bin` = '$bin', `caja` = '$caja', `costo` = '$costo', `nombre` = '$nombre', `numero` = '$numero', `maximo` = '$maximo', `minimo` = '$minimo' WHERE `inventario`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Material editado exitosamente";
        header("Location: bom.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el material, contacte a soporte";
        header("Location: bom.php");
        exit(0);
    }
}

if (isset($_POST['save'])) {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $proveedor = mysqli_real_escape_string($con, $_POST['proveedor']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $marca = mysqli_real_escape_string($con, $_POST['marca']);
    $condicion = mysqli_real_escape_string($con, $_POST['condicion']);
    $costo = mysqli_real_escape_string($con, $_POST['costo']);

    $query = "INSERT INTO bom SET nombre='$nombre', proveedor='$proveedor',descripcion='$descripcion',marca='$marca',condicion='$condicion',costo='$costo'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Registro un nuevo bom, nombre: $nombre, costo: $costo', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Material registrado exitosamente";
        header("Location: bom.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al registrar el material, contacte a soporte";
        header("Location: bom.php");
        exit(0);
    }
}
