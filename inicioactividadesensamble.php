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
                        
                    }
                });
            });
        </script>";
}

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
                        $message = "Se inicio la asignación exitosamente a las $hora_actual";
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
                        unset($_SESSION['message']);
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
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="sb-nav-fixed" style="background-color: #e7e7e7;">
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-4">

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

                ?>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header pt-3">
                                        <?php
                                        if ($registro['estatusplano'] === '1') {
                                            echo '<h4>ENSAMBLE EN PROGRESO</h4>';
                                        } else if ($registro['estatusplano'] === '2') {
                                            echo '<h4>ENSAMBLE EN PAUSA</h4>';
                                        } else {
                                            echo "Error, contacte a soporte";
                                        }
                                        ?>

                                    </div>
                                    <div class="card-body">
                                        <?php include 'message.php'; ?>
                                        <form action="codeactividad.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" id="nombre" value="<?= $registro['nombre']; ?>" disabled>
                                                    <label for="nombre">Nombre del proyecto</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                    <input type="text" class="form-control" id="nombreplano" name="nombreplano" value="<?= $registro['nombreplano']; ?>" readonly>
                                                    <label for="nombreplano">Nombre del diagrama</label>
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
                                                    echo '<div class="col-5 mt-3">
                                                                <img src="images/ensamble.gif" alt="">
                                                            </div>';
                                                } else if ($registro['estatusplano'] === '2') {
                                                    echo '<div class="col-5 m-4">
                                                                <img src="images/ensamble.png" alt="">
                                                            </div>';
                                                } else {
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
                                                                <button type="submit" name="restartensamble" class="btn btn-success m-3">Reiniciar actividad</button>
                                                            </form>';
                                                    } else {
                                                        echo "Error, contacte a soporte";
                                                    }
                                                    ?>

                                                    <?php
                                                    if (empty($registro['medio'])) {
                                                    ?>
                                                        <button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Ver actividad</button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="pdfModalLabel">Actividad <?= $registro['nombreplano']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p><?= $registro['actividad']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php

                                                    } else {
                                                    ?>
                                                        <button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Ver diagrama</button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="pdfModalLabel">Diagrama <?= $registro['nombreplano']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <iframe src="data:application/pdf;base64,<?= base64_encode($registro['medio']); ?>" width="100%" height="600px"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php

                                                    }
                                                    ?>



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

                                                                            $query = "SELECT * FROM motivosensamble";
                                                                            $result = mysqli_query($con, $query);

                                                                            // Comprobamos si hay resultados
                                                                            if (mysqli_num_rows($result) > 0) {
                                                                                echo '<div class="row">';
                                                                                // Iteramos sobre los resultados para generar los radio buttons en dos columnas
                                                                                while ($row = mysqli_fetch_assoc($result)) {
                                                                                    echo '<div class="col-md-6 mb-3" style="text-align:left;">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="radio" name="motivosparo" id="' . $row['motivosparo'] . '" value="' . $row['motivosparo'] . '">
                                                                                                <label class="form-check-label" for="' . $row['motivosparo'] . '">' . $row['motivosparo'] . '</label>
                                                                                            </div>
                                                                                        </div>';
                                                                                }
                                                                                echo '</div>';
                                                                            } else {
                                                                                echo 'No hay motivos disponibles';
                                                                            }
                                                                            ?>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <div id="botonPausar" style="display: block;">
                                                                        <button type="submit" class="btn btn-primary" name="saveensamble">Detener actividad</button>
                                                                    </div>
                                                                    <div id="botonTerminar" style="display: none;">
                                                                        <button type="submit" class="btn btn-warning" name="finishensamble">Terminar</button>
                                                                    </div>
                                                                    <div id="botonMenu" style="display: none;">
                                                                        <button type="submit" class="btn btn-primary" name="pausarensamble">Regresar a ensamble</button>
                                                                    </div>
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
                        echo "<h4>No se encontro ningun registro, contacte a soporte</h4>";
                    }
                }
?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        // Obtener los radio buttons y los divs de los botones "Terminar", "Pausar" y "Menú"
        const radioButtons = document.querySelectorAll('input[name="motivosparo"]');
        const divBotonTerminar = document.getElementById('botonTerminar');
        const divBotonPausar = document.getElementById('botonPausar');
        const divBotonMenu = document.getElementById('botonMenu');

        // Escuchar el evento de cambio en los radio buttons
        radioButtons.forEach(radioButton => {
            radioButton.addEventListener('change', function(event) {
                const valorSeleccionado = event.target.value;

                // Mostrar u ocultar los botones dependiendo del valor seleccionado
                if (valorSeleccionado === 'Trabajo terminado') {
                    divBotonTerminar.style.display = 'block'; // Mostrar el botón "Terminar"
                    divBotonPausar.style.display = 'none'; // Ocultar el botón "Pausar"
                    divBotonMenu.style.display = 'none'; // Ocultar el botón "Menú"
                } else if (valorSeleccionado === 'Atención a otra prioridad' || valorSeleccionado === 'Fin de jornada laboral') {
                    divBotonTerminar.style.display = 'none'; // Ocultar el botón "Terminar"
                    divBotonPausar.style.display = 'none'; // Ocultar el botón "Pausar"
                    divBotonMenu.style.display = 'block'; // Mostrar el botón "Menú"
                } else {
                    divBotonTerminar.style.display = 'none'; // Ocultar el botón "Terminar"
                    divBotonPausar.style.display = 'block'; // Mostrar el botón "Pausar"
                    divBotonMenu.style.display = 'none'; // Ocultar el botón "Menú"
                }
            });
        });
    </script>


</body>

</html>