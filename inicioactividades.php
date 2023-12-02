<?php
session_start();
require 'dbcon.php';

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $idcodigo = $_SESSION['codigo'];
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $id_plano = $_GET['id'];

    // Consultar el estatus del plano en la base de datos
    $queryEstatus = "SELECT estatusplano FROM plano WHERE id = $id_plano";
    $resultEstatus = mysqli_query($con, $queryEstatus);

    if ($resultEstatus) {
        if (mysqli_num_rows($resultEstatus) > 0) {
            $rowEstatus = mysqli_fetch_assoc($resultEstatus);
            $estatusPlano = $rowEstatus['estatusplano'];

            // Verificar que el estatus sea 1
            if ($estatusPlano == 1) {
                // El estatus del plano es 1, puedes proceder con la acción
                $query = "SELECT nombreplano FROM plano WHERE id = $id_plano";
                $result = mysqli_query($con, $query);
                $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        // Obtener el nombreplano si se encontró un registro
                        $row = mysqli_fetch_assoc($result);
                        $nombreplano = $row['nombreplano'];

                        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Inicio actividades en el plano $nombreplano', hora='$hora_actual', fecha='$fecha_actual'";
                        $query_rundos = mysqli_query($con, $querydos);
                        $_SESSION['message'] = "Se inicio la asignación exitosamente a las $hora_actual";
                    }
                }
            } else {
            }
        }
    }
} else {
    // No hay sesión activa, redirigir a la página de inicio de sesión
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Actividad en progreso | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-4">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <?php include('message.php'); ?>
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
                                                    <input type="text" class="form-control" id="nombreplano" name="nombreplano" value="<?= $registro['nombreplano']; ?>" readonly>
                                                    <label for="nombreplano">Nombre del plano</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                    <input type="text" class="form-control" id="nivel" value="<?= $registro['nivel']; ?>" disabled>
                                                    <label for="nivel">Nivel </label>
                                                </div>

                                                <div class="form-floating col-4 mt-3">
                                                    <input type="text" class="form-control" id="piezas" value="<?= $registro['piezas']; ?>" disabled>
                                                    <label for="piezas">Numero de piezas</label>
                                                </div>

                                                <?php
                                                            if ($registro['estatusplano'] === '1') {
                                                                echo '<div class="col-5">
                                                                <img src="images/actividad.gif" alt="">
                                                            </div>';
                                                            } else if ($registro['estatusplano'] === '2') {
                                                                echo '<div class="col-5 m-4">
                                                                <img src="images/pausa.png" alt="">
                                                            </div>';
                                                            }  else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?>

                                                <div class="col-6 text-center mt-3 d-flex align-items-center justify-content-center">
                                                <?php
                                                            if ($registro['estatusplano'] === '1') {
                                                                echo "<button type='button' data-bs-toggle='modal' data-bs-target='#exampleModal' class='btn btn-danger m-3'>
                                                                Detener actividad
                                                            </button>";
                                                            } else if ($registro['estatusplano'] === '2') {
                                                                echo '<form action="codeactividad.php" method="post">
                                                                <button type="submit" name="restart" class="btn btn-success m-3">Reiniciar actividad</button>
                                                            </form>';
                                                            }  else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?>
                                                            
                                                    <button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Ver plano</button>
                                                    <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="pdfModalLabel"><?= $registro['nombreplano']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <iframe src="data:application/pdf;base64,<?= base64_encode($registro['medio']); ?>" width="100%" height="600px"></iframe>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">MOTIVO DE PARO</h1>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="row">
                                                                        <div class="form-floating col-12">
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
                                                                            <label for="estatus">Motivo de paro</label>
                                                                        </div>
                                                                    </div>


                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary" name="save">Pausar actividad</button>
                                                                    <form action="codeactividad.php" method="post">
                                                                        <button type="submit" class="btn btn-warning" name="finish">Terminar</button>
                                                                    </form>
                                                                </div>
                                        </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>