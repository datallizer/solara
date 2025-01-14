<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Editando registro de proyectos' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Editar proyecto | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-5">

                <div class="row justify-content-center">
                    <div class="col-md-12">


                        <?php

                        if (isset($_GET['id'])) {
                            $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                            $query = "SELECT * FROM proyecto WHERE id='$registro_id' ";
                            $query_run = mysqli_query($con, $query);

                            if (mysqli_num_rows($query_run) > 0) {
                                $registro = mysqli_fetch_array($query_run);
                                $prioridad_actual = $registro['prioridad'];
                                // $diseño_actual = $registro['etapadiseño'];
                                // $control_actual = $registro['etapacontrol'];
                                // $tcontrol_actual = $registro['etapatcontrol'];
                                // $mecanica_actual = $registro['etapamecanica'];
                                $estatus_actual = $registro['estatus'];
                                $etapa_actual = $registro['etapa'];

                        ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 style="text-transform: uppercase;">EDITAR PROYECTO <?= $registro['nombre']; ?>
                                            <?php
                                            if ($registro['estatus'] == 1) {
                                            ?>
                                                <a href="proyectos.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                            <?php
                                            } elseif ($registro['estatus'] == 2) {
                                            ?>
                                                <a href="anteproyectos.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                            <?php
                                            }
                                            ?>

                                        </h4>
                                    </div>
                                    <div class="card-body">

                                        <form action="codeproyecto.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <?php
                                                    $readonly = (isset($_SESSION['rol']) && !in_array($_SESSION['rol'], [1, 2])) ? 'readonly' : '';
                                                    ?>
                                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>" <?= $readonly ?>>
                                                    <label for="nombre">Nombre del proyecto</label>
                                                </div>


                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="cliente" id="cliente" value="<?= $registro['cliente']; ?>">
                                                    <label for="cliente">Cliente</label>
                                                </div>

                                                <?php
                                                if ($registro['estatus'] == 1  || $registro['estatus'] == 2 || $registro['estatus'] == 0) {
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2,5,9,13])) {
                                                        echo '<div class="form-floating col-12 col-md-6 mt-3">
                                                        <input type="text" class="form-control" name="presupuesto" id="presupuesto" value="' . $registro['presupuesto'] . '">
                                                        <label for="presupuesto">Presupuesto</label>
                                                    </div>
                                                    <div class="form-floating col-7 mt-3">
                                                        <input type="date" class="form-control" name="fechainicio" id="fechainicio" value="' . $registro['fechainicio'] . '">
                                                        <label for="fechainicio">Fecha de inicio</label>
                                                    </div>

                                                    <div class="form-floating col-5 mt-3">
                                                        <input type="date" class="form-control" name="fechafin" id="fechafin" value="' . $registro['fechafin'] . '">
                                                        <label for="fechafin">Fecha de finalización</label>
                                                    </div>

                                                    <div class="form-floating col-3 mt-3">
                                                        <select class="form-select" name="estatus" id="estatus">
                                                        <option disabled>Seleccione un estatus</option>
                                                        <option value="0" ' . ($estatus_actual == 0 ? 'selected' : '') . '>Inactivo</option>
                                                        <option value="1" ' . ($estatus_actual == 1 ? 'selected' : '') . '>Activo</option>
                                                        <option value="2" ' . ($estatus_actual == 2 ? 'selected' : '') . '>Anteproyecto</option>
                                                        </select>
                                                        <label for="estatus">Estatus</label>
                                                    </div>

                                                    <div class="form-floating col-3 mt-3">
                                                        <select class="form-select" name="prioridad" id="prioridad">
                                                        <option disabled>Seleccione la prioridad</option>';
                                                        for ($i = 1; $i <= 30; $i++) {
                                                            echo '<option value="' . $i . '" ' . ($prioridad_actual == $i ? 'selected' : '') . '>Nivel ' . $i . '</option>';
                                                        }
                                                        echo '</select>
                                                    <label for="prioridad">Prioridad</label>
                                                    </div>';
                                                    }
                                                } elseif ($registro['estatus'] == 2) {
                                                    echo '
                                                    <div class="form-floating col-6 mt-3">
                                                        <select class="form-select" name="estatus" id="estatus">
                                                        <option disabled>Seleccione un estatus</option>
                                                        <option value="2" ' . ($estatus_actual == 2 ? 'selected' : '') . '>Anteproyecto</option>
                                                        <option value="1" ' . ($estatus_actual == 1 ? 'selected' : '') . '>Rechazado</option>
                                                        </select>
                                                        <label for="estatus">Estatus</label>
                                                    </div>';
                                                }
                                                ?>

                                                <?php
                                                if ($registro['estatus'] == 1) {
                                                ?>
                                                <div class="form-floating col-6 mt-3">
                                                    <select class="form-select" name="etapa" id="etapa">
                                                        <option disabled>Seleccione una etapa</option>
                                                        <option disabled>------- Ejecución -------</option>
                                                        <option value="7" <?= ($etapa_actual == 6) ? 'selected' : ''; ?>>Recepción de PO</option>
                                                        <option value="7" <?= ($etapa_actual == 7) ? 'selected' : ''; ?>>Kick off meeting</option>
                                                        <option value="8" <?= ($etapa_actual == 8) ? 'selected' : ''; ?>>Visita formal de levantamiento</option>
                                                        <option value="9" <?= ($etapa_actual == 9) ? 'selected' : ''; ?>>Prediseño (mecánico y eléctrico)</option>
                                                        <option value="10" <?= ($etapa_actual == 10) ? 'selected' : ''; ?>>Revisión de diseño/aprobación de cliente</option>
                                                        <option value="11" <?= ($etapa_actual == 11) ? 'selected' : ''; ?>>Actualización de BOM</option>
                                                        <option value="12" <?= ($etapa_actual == 12) ? 'selected' : ''; ?>>Colocación de PO's</option>
                                                        <option value="13" <?= ($etapa_actual == 13) ? 'selected' : ''; ?>>Construcción del equipo</option>
                                                        <option value="14" <?= ($etapa_actual == 14) ? 'selected' : ''; ?>>Pruebas internas iniciales</option>
                                                        <option value="15" <?= ($etapa_actual == 15) ? 'selected' : ''; ?>>Debugging interno y pruebas secundarias</option>
                                                        <option value="16" <?= ($etapa_actual == 16) ? 'selected' : ''; ?>>Buf off interno</option>
                                                        <option value="17" <?= ($etapa_actual == 17) ? 'selected' : ''; ?>>Buy off con cliente</option>
                                                        <option value="18" <?= ($etapa_actual == 18) ? 'selected' : ''; ?>>Empaque y envío a instalaciones de cliente</option>
                                                        <option disabled>------- Validación -------</option>
                                                        <option value="19" <?= ($etapa_actual == 19) ? 'selected' : ''; ?>>Instalación con cliente</option>
                                                        <option value="20" <?= ($etapa_actual == 20) ? 'selected' : ''; ?>>Arranque y validación de equipo (buy off)</option>
                                                        <option value="21" <?= ($etapa_actual == 21) ? 'selected' : ''; ?>>Entrenamiento</option>
                                                    </select>
                                                    <label for="etapa">Etapa de proyecto</label>
                                                </div>
                                                <?php
                                                } elseif ($registro['estatus'] == 2) {
                                                ?>
                                                <div class="form-floating col-6 mt-3">
                                                    <select class="form-select" name="etapa" id="etapa">
                                                        <option disabled>Seleccione una etapa</option>
                                                        <option disabled>------- Pretrabajo -------</option>
                                                        <option value="2" <?= ($etapa_actual == 2) ? 'selected' : ''; ?>>Visita/levantamiento con cliente</option>
                                                        <option value="3" <?= ($etapa_actual == 3) ? 'selected' : ''; ?>>Generación de diseño/diagrama a bloques</option>
                                                        <option value="4" <?= ($etapa_actual == 4) ? 'selected' : ''; ?>>Generación de BOM's</option>
                                                        <option value="5" <?= ($etapa_actual == 5) ? 'selected' : ''; ?>>Cotización</option>
                                                    </select>
                                                    <label for="etapa">Etapa de proyecto</label>
                                                </div>
                                                <?php
                                                }
                                                ?>
                                                

                                                <?php
                                                if ($registro['estatus'] == 1  || $registro['estatus'] == 2  || $registro['estatus'] == 0) {
                                                ?>
                                                    <div class="form-floating col-12 mt-3">
                                                        <textarea type="text" class="form-control" name="detalles" id="detalles" style="min-height:150px;"><?= $registro['detalles']; ?></textarea>
                                                        <label for="detalles">Detalles</label>
                                                    </div>
                                                <?php
                                                }
                                                ?>


                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar proyecto
                                                    </button>
                                                </div>


                                            </div>
                                    </div>

                                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>