<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';

if (!empty($message)) {
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

if (isset($_SESSION['codigo'])) {
    $idcodigo = $_SESSION['codigo'];
    $nombre = $_SESSION['nombre'];
    $apellidop = $_SESSION['apellidop'];
    $id_plano = $_GET['id'];

    $queryEstatus = "SELECT estatusplano FROM plano WHERE id = $id_plano";
    $resultEstatus = mysqli_query($con, $queryEstatus);

    if ($resultEstatus) {
        if (mysqli_num_rows($resultEstatus) > 0) {
            $rowEstatus = mysqli_fetch_assoc($resultEstatus);
            $estatusPlano = $rowEstatus['estatusplano'];

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
                        $queryubicacion_run = mysqli_query($con, $queryubicacion);
                        $message = "Se inicio la asignación $nombreplano exitosamente a las $hora_actual";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<style>
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
    }

    .spinner-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .spinner {
        width: 3rem;
        height: 3rem;
    }
</style>

<body class="sb-nav-fixed" style="background-color: #e7e7e7;">
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid g-0">
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
                        <div class="row justify-content-center g-0">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header pt-3" style="border-radius: 0px;">
                                        <?php
                                        if ($registro['estatusplano'] === '3') {
                                            echo '<h1 class="text-center"><b>MAQUINADO EN PROGRESO</b></h1>';
                                        } else {
                                            echo '<h1 class="text-center"><b>MAQUINADO EN PAUSA</b></h1>';
                                        }
                                        ?>

                                    </div>
                                    <div class="card-body" style="min-height: 91vh;">
                                        <?php include 'message.php'; ?>
                                        <form id="actividadForm" action="codeactividad.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row justify-content-between mt-1 p-2">
                                                <div class="col-12 text-center p-3" style="background-color: #e7e7e7;border-radius:10px;">
                                                    <h5 style="margin-bottom: 0px;">Proyecto</h5>
                                                    <p style="font-size: 40px;"><?= $registro['nombre']; ?></p>
                                                </div>

                                                <div class="col-12 text-center p-3 mt-3" style="background-color: #e7e7e7;border-radius:10px;">
                                                    <h5 style="margin-bottom: 0px;">Plano</h5>
                                                    <p style="font-size: 40px;"><?= $registro['nombreplano']; ?></p>
                                                </div>

                                                <div class="col-2 text-center p-3 mt-3 d-flex align-items-center justify-content-center" style="background-color: #e7e7e7;border-radius:10px;">
                                                    <div>

                                                        <h5 style="margin-bottom: 0px;">Nivel</h5>
                                                        <p style="font-size: 40px;"><?= $registro['nivel']; ?></p>
                                                    </div>
                                                </div>

                                                <div class="col-3 text-center p-3 mt-3 d-flex align-items-center justify-content-center" style="background-color: #e7e7e7;border-radius:10px;">
                                                    <div>
                                                        <h5 style="margin-bottom: 0px;">Número de piezas</h5>
                                                        <p style="font-size: 40px;"><?= $registro['piezas']; ?></p>
                                                    </div>
                                                </div>

                                                <?php
                                                if ($registro['estatusplano'] === '3') {
                                                    echo '<div class="col-3 mt-3">
                                                                <img src="images/progreso.gif" alt="">
                                                            </div>';
                                                } else {
                                                    echo '<div class="col-3 m-4">
                                                                <img src="images/pausa.jpg" alt="">
                                                            </div>';
                                                }
                                                ?>

                                                <div class="col-3 text-center mt-3 d-flex align-items-center justify-content-center">
                                                    <?php
                                                    if ($registro['estatusplano'] === '3') {
                                                        echo "<button type='button' data-bs-toggle='modal' data-bs-target='#exampleModal' class='btn btn-danger m-3' style='font-size:21px;'>
                                                                <i class='bi bi-sign-stop'></i> Detener
                                                            </button>";
                                                    } else {
                                                        echo '<form action="codeactividad.php" method="post">
                                                                <button type="submit" name="restart" class="btn btn-success m-3" style="font-size:21px;"><i class="bi bi-arrow-clockwise"></i> Reiniciar</button>
                                                            </form>';
                                                    }
                                                    ?>

                                                    <?php
                                                    if (empty($registro['medio'])) {
                                                    ?>
                                                        <button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>" style="font-size:21px;">Actividad</button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 style="font-size: 40px;" class="modal-title" id="pdfModalLabel"><?= $registro['nombreplano']; ?></h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p style="font-size: 35px;"><?= $registro['actividad']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php

                                                    } else {
                                                    ?>
                                                        <button type="button" class="btn btn-primary m-3" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>" style="font-size:21px;">Ver plano</button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 style="font-size: 40px;" class="modal-title" id="pdfModalLabel"><?= $registro['nombreplano']; ?></h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <iframe src="<?= $registro['medio']; ?>" width="100%" height="600px"></iframe>
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
                                                                    <h1 style="font-size: 40px;" class="modal-title fs-5" id="exampleModalLabel">MOTIVO DE PARO</h1>
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
                                                                        <button type="submit" class="btn btn-primary" name="save">Detener actividad</button>
                                                                    </div>
                                                                    <div id="botonTerminar" style="display: none;">
                                                                        <button type="submit" class="btn btn-warning" name="finish">Terminar</button>
                                                                    </div>
                                                                    <div id="botonMenu" style="display: none;">
                                                                        <button type="submit" class="btn btn-primary" name="pausar">Regresar</button>
                                                                    </div>
                                                                    <div id="botonLunch" style="display: none;">
                                                                        <button type="submit" class="btn btn-primary" name="lunchEnd">Salir a Lunch</button>
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

    <div class="spinner-overlay" style="z-index: 9999;">
        <div class="spinner-container">
            <div class="spinner-grow text-primary spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Activar modo pantalla completa',
                text: 'Debes activar el modo pantalla completa para continuar.',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.setItem('fullscreenAccepted', 'true');
                    requestFullscreen();
                }
            });

        });

        function requestFullscreen() {
            let elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) { // Firefox
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) { // Chrome, Safari and Opera
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { // IE/Edge
                elem.msRequestFullscreen();
            }
        }
        // Obtener los radio buttons y los divs de los botones "Terminar", "Pausar" y "Menú"
        const radioButtons = document.querySelectorAll('input[name="motivosparo"]');
        const divBotonTerminar = document.getElementById('botonTerminar');
        const divBotonPausar = document.getElementById('botonPausar');
        const divBotonMenu = document.getElementById('botonMenu');
        const divBotonLunch = document.getElementById('botonLunch');

        // Escuchar el evento de cambio en los radio buttons
        radioButtons.forEach(radioButton => {
            radioButton.addEventListener('change', function(event) {
                const valorSeleccionado = event.target.value;

                // Mostrar u ocultar los botones dependiendo del valor seleccionado
                if (valorSeleccionado === 'Pieza terminada') {
                    divBotonTerminar.style.display = 'block'; // Mostrar el botón "Terminar"
                    divBotonPausar.style.display = 'none'; // Ocultar el botón "Pausar"
                    divBotonMenu.style.display = 'none'; // Ocultar el botón "Menú"
                    divBotonLunch.style.display = 'none';
                } else if (valorSeleccionado === 'Atención a otra prioridad' || valorSeleccionado === 'Fin de jornada laboral' || valorSeleccionado === 'Falta de material') {
                    divBotonTerminar.style.display = 'none'; // Ocultar el botón "Terminar"
                    divBotonPausar.style.display = 'none'; // Ocultar el botón "Pausar"
                    divBotonMenu.style.display = 'block'; // Mostrar el botón "Menú"
                    divBotonLunch.style.display = 'none';
                } else if (valorSeleccionado === 'Lunch') {
                    divBotonTerminar.style.display = 'none'; // Ocultar el botón "Terminar"
                    divBotonPausar.style.display = 'none'; // Ocultar el botón "Pausar"
                    divBotonMenu.style.display = 'none'; // Ocultar el botón "Menú"
                    divBotonLunch.style.display = 'block';
                } else {
                    divBotonTerminar.style.display = 'none'; // Ocultar el botón "Terminar"
                    divBotonPausar.style.display = 'block'; // Mostrar el botón "Pausar"
                    divBotonMenu.style.display = 'none'; // Ocultar el botón "Menú"
                    divBotonLunch.style.display = 'none';
                }
            });
        });
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault(); // Evitar el envío inmediato del formulario

                // Mostrar el overlay con el spinner
                $('.spinner-overlay').show();

                // Obtener el valor del botón que se hizo clic
                var buttonName = $(this).find('button[type=submit]:focus').attr('name');

                // Simular un retraso para demostrar el spinner (elimina esto en producción)
                var form = this;
                // Agregar el valor del botón como un campo oculto al formulario
                $('<input>').attr({
                    name: buttonName
                }).appendTo(form);

                // Enviar el formulario después del retraso simulado
                form.submit();

            });


            // function verificarNuevasActividades() {
            //     $.ajax({
            //         url: 'verificar_actividades.php', // Ruta del archivo PHP
            //         method: 'GET',
            //         dataType: 'json',
            //         success: function(response) {
            //             let nuevas_actividades = response.nuevas_actividades;
            //             let actividades_anteriores = response.actividades_anteriores;
            //             let nombreplano = response.nombreplano;

            //             if (nuevas_actividades > actividades_anteriores) {
            //                 Swal.fire({
            //                     title: 'Nueva actividad asignada',
            //                     text: `${nombreplano}`,
            //                     icon: 'info',
            //                     confirmButtonText: 'Aceptar'
            //                 });
            //             }
            //         },
            //         error: function(xhr, status, error) {
            //             console.error('Error al verificar nuevas actividades:', error);
            //         }
            //     });
            // }


            // // Llama a la función al cargar la página
            // verificarNuevasActividades();

            // // Opcional: Puedes configurar un intervalo para verificar periódicamente
            // setInterval(verificarNuevasActividades, 100); // Verifica cada minuto
        });
    </script>


</body>

</html>