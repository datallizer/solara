<?php
require 'dbcon.php';

if(isset($_POST['delete']))
{
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Usuario eliminado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al eliminar el usuario, contácte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}

if(isset($_POST['update']))
{
    $id = mysqli_real_escape_string($con,$_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    // Encriptar la nueva contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidop` = '$apellidop', `apellidom` = '$apellidom', `codigo` = '$codigo', `rol` = '$rol' WHERE `usuarios`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Usuario editado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}



if(isset($_POST['save']))
{
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidop = mysqli_real_escape_string($con, $_POST['apellidop']);
    $apellidom = mysqli_real_escape_string($con, $_POST['apellidom']);
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);

    $query = "INSERT INTO usuarios SET nombre='$nombre', apellidop='$apellidop', apellidom='$apellidom', codigo='$codigo', estatus='1', rol='$rol'";

    $query_run = mysqli_query($con, $query);
    if($query_run)
    {
        $_SESSION['message'] = "Usuario creado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Error al crear el usuario, contacte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}


?>