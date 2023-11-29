<?php
session_start();
require 'dbcon.php';
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
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
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
                                <h4>ACTIVIDAD EN PROGRESO</h4>
                            </div>
                            <div class="card-body">

                                <?php

                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT proyecto.*, plano.*
                                    FROM plano 
                                    JOIN proyecto ON plano.idproyecto = proyecto.id 
                                    JOIN asignacionplano ON asignacionplano.idplano = plano.id 
                                    JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo WHERE plano.id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);

                                ?>

                                        <form action="codeactividad.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" id="nombre" value="<?= $registro['nombre']; ?>" disabled>
                                                    <label for="nombre">Nombre del proyecto</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                    <input type="text" class="form-control" id="nombreplano" value="<?= $registro['nombreplano']; ?>" disabled>
                                                    <label for="nombreplano">Nombre del plano</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-8 mt-3">
                                                    <input type="text" class="form-control" id="nivel" value="<?= $registro['nivel']; ?>" disabled>
                                                    <label for="nivel">Nivel </label>
                                                </div>

                                                <div class="form-floating col-5 mt-3">
                                                    <input type="text" class="form-control" id="piezas" value="<?= $registro['piezas']; ?>" disabled>
                                                    <label for="piezas">Numero de piezas</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-7 mt-3">
                                                    <?php
                                                    // Tu código de conexión a la base de datos aquí

                                                    $query = "SELECT * FROM motivos";
                                                    $result = mysqli_query($con, $query);

                                                    // Comprobamos si hay resultados
                                                    if (mysqli_num_rows($result) > 0) {
                                                        echo '<select class="form-select" name="motivosparo" id="motivosparo">';
                                                        // Iteramos sobre los resultados para generar las opciones del select
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $row['motivosparo'] . '">' . $row['motivosparo'] . '</option>';
                                                        }
                                                        echo '</select>';
                                                    } else {
                                                        echo 'No hay motivos disponibles';
                                                    }
                                                    ?>
                                                    <label for="estatus">Estatus</label>
                                                </div>

                                                <div class="col-5">
                                                    <img src="images/actividad.gif" alt="">
                                                </div>

                                                <div class="col-6 text-center mt-3 d-flex align-items-center justify-content-center">
                                                    <button type="submit" name="save" class="btn btn-danger">
                                                        Detener actividad
                                                    </button>
                                                </div>


                                            </div>
                            </div>

                            </form>
                    <?php
                                    } else {
                                        echo "<h4>No Such Id Found</h4>";
                                    }
                                }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO USUARIO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeusuarios.php" method="POST">
                        <div class="row">
                            <div class="col-12 mtop">
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            </div>

                            <div class="col-6 mtop">
                                <input type="text" class="form-control" name="apellidop" id="apellidop" placeholder="Apellido paterno" autocomplete="off" required>
                            </div>

                            <div class="col-6 mtop">
                                <input type="text" class="form-control" name="apellidom" id="apellidom" placeholder="Apellido materno" autocomplete="off" required>
                            </div>

                            <div class="col-5 mtop">
                                <input type="text" class="form-control" name="username" id="username" placeholder="Nombre de usuario" autocomplete="off" required>
                            </div>

                            <div class="col-7 mtop">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" required>
                            </div>

                            <div class="col-12 mtop">
                                <select class="form-select" name="rol" id="rol" autocomplete="off" required>
                                    <option disabled>Categoría</option>
                                    <option value="1">Bachillerato</option>
                                    <option value="2">Licenciatura escolarizada</option>
                                    <option value="3">Licenciatura ejecutiva</option>
                                    <option value="4">Administrador</option>
                                    <option value="5">Control escolar</option>
                                </select>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>