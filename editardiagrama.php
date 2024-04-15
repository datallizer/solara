<?php
session_start();
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión

if (!empty($message)) {
    // HTML y JavaScript para mostrar la alerta...
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
                    title: 'NOTIFICACIÓN',
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['message']); // Limpiar el mensaje de la sesión
}

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Editando registro de ensamble' WHERE `usuarios`.`codigo` = '$codigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
    } else {
        // Redirigir al usuario a una página de inicio de sesión
        header('Location: login.php');
        exit(); // Finalizar el script después de la redirección
    }
} else {
    // Redirigir al usuario a una página de inicio de sesión si no hay una sesión activa
    header('Location: login.php');
    exit(); // Finalizar el script después de la redirección
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar maquinado | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-5">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>EDITAR ENSAMBLE
                                    <a href="ensamble.php" class="btn btn-danger btn-sm float-end">
                                        Regresar
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php

                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT proyecto.*, diagrama.*
                                    FROM diagrama 
                                    JOIN proyecto ON diagrama.idproyecto = proyecto.id 
                                    JOIN asignaciondiagrama ON asignaciondiagrama.idplano = diagrama.id 
                                    JOIN usuarios ON asignaciondiagrama.codigooperador = usuarios.codigo WHERE diagrama.id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $pieza_actual = $registro['nivel'];
                                        $estatus_actual = $registro['estatusplano'];

                                ?>

                                        <form action="codediagramas.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                    <input type="text" class="form-control" id="nombreplano" name="nombreplano" value="<?= $registro['nombreplano']; ?>">
                                                    <label for="nombreplano">Nombre del diagrama</label>
                                                </div>

                                                <div class="col-12 col-md-7 mt-3 form-floating">
                                                    <select class="form-select" name="nivel" id="nivel">
                                                        <option disabled>Seleccione el nivel</option>
                                                        <option value="1" <?= ($pieza_actual == 1) ? 'selected' : ''; ?>>Nivel 1</option>
                                                        <option value="2" <?= ($pieza_actual == 2) ? 'selected' : ''; ?>>Nivel 2</option>
                                                        <option value="3" <?= ($pieza_actual == 3) ? 'selected' : ''; ?>>Nivel 3</option>
                                                        <option value="4" <?= ($pieza_actual == 4) ? 'selected' : ''; ?>>Nivel 4</option>
                                                    </select>
                                                    <label style="margin-left: 10px;" for="nivel">Nivel de pieza</label>
                                                </div>

                                                <div class="form-floating col-5 mt-3">
                                                    <input type="text" class="form-control" id="piezas" name="piezas" value="<?= $registro['piezas']; ?>">
                                                    <label for="piezas">Número de piezas</label>
                                                </div>

                                                <div class="col-12 col-md-7 mt-3 form-floating">
                                                    <select class="form-select" name="estatusplano" id="estatusplano">
                                                        <option disabled>Seleccione un estatus</option>
                                                        <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Terminado</option>
                                                        <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Asignado</option>
                                                        <option value="2" <?= ($estatus_actual == 2) ? 'selected' : ''; ?>>Pausado</option>
                                                        <option value="3" <?= ($estatus_actual == 3) ? 'selected' : ''; ?>>En proceso</option>
                                                    </select>
                                                    <label style="margin-left: 10px;" for="estatusplano">Estatus del plano</label>
                                                </div>

                                                <?php
                                                // Verifica si 'medio' está vacío o no
                                                if (empty($registro['medio'])) {
                                                ?>
                                                    <div class="form-floating col-7 mt-3">
                                                        <input type="text" class="form-control" id="actividad" name="actividad" value="<?= $registro['actividad']; ?>">
                                                        <label for="actividad">Actividad</label>
                                                    </div>
                                                <?php
                                                 } else {
                                                    ?>
                                                        <div class="mt-3">
                                                            <label for="medio" class="form-label">Nuevo Diagrama PDF</label>
                                                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
                                                        </div>
                                                        <input type="hidden" class="form-control" id="actividad" name="actividad" value="">
                                                    <?php
                                                    }
                                                    ?>

                                                <div class="col-12 text-center mt-3 d-flex align-items-center justify-content-center">
                                                    <button type="submit" name="update" class="btn btn-warning">
                                                        Actualizar
                                                    </button>
                                                </div>
                                            </div>
                            </div>
                            </form>
                    <?php
                                    } else {
                                        echo "<h4>No se encontro ningun registro con este ID</h4>";
                                    }
                                }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>