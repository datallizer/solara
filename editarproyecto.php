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
    <link rel="stylesheet" href="css/admin.css">
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
                                $diseño_actual = $registro['etapadiseño'];
                                $control_actual = $registro['etapacontrol'];
                                $tcontrol_actual = $registro['etapatcontrol'];
                                $mecanica_actual = $registro['etapamecanica'];
                                $estatus_actual = $registro['estatus'];

                        ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 style="text-transform: uppercase;">EDITAR PROYECTO <?= $registro['nombre']; ?>
                                            <a href="proyectos.php" class="btn btn-danger btn-sm float-end">Regresar</a>
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
                                                    <input type="text" class="form-control" name="cliente" id="cliente" value="<?= $registro['cliente']; ?>" readonly>
                                                    <label for="cliente">Cliente</label>
                                                </div>

                                                <?php
                                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
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
                                                ?>



                                                <div class="form-floating col-3 mt-3">
                                                    <select class="form-select" name="etapadiseño" id="etapadiseño">
                                                        <option disabled>Seleccione la etapa</option>
                                                        <option value="1" <?= ($diseño_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                        <option value="2" <?= ($diseño_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                        <option value="3" <?= ($diseño_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                        <option value="4" <?= ($diseño_actual == 4) ? 'selected' : ''; ?>>Planos</option>
                                                        <option value="5" <?= ($diseño_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                        <option value="6" <?= ($diseño_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                        <option value="7" <?= ($diseño_actual == 7) ? 'selected' : ''; ?>>Remediación</option>
                                                        <option value="8" <?= ($diseño_actual == 8) ? 'selected' : ''; ?>>Documentación</option>
                                                    </select>
                                                    <label for="etapadiseño">Etapa de diseño</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                    <select class="form-select" name="etapacontrol" id="etapacontrol">
                                                        <option disabled>Seleccione la etapa</option>
                                                        <option value="1" <?= ($control_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                        <option value="2" <?= ($control_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                        <option value="3" <?= ($control_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                        <option value="4" <?= ($control_actual == 4) ? 'selected' : ''; ?>>Diagramas</option>
                                                        <option value="5" <?= ($control_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                        <option value="6" <?= ($control_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                        <option value="7" <?= ($control_actual == 7) ? 'selected' : ''; ?>>Programación</option>
                                                        <option value="8" <?= ($control_actual == 8) ? 'selected' : ''; ?>>Debugging</option>
                                                        <option value="9" <?= ($control_actual == 9) ? 'selected' : ''; ?>>Documentación</option>
                                                    </select>
                                                    <label for="etapacontrol">Etapa de control</label>
                                                </div>

                                                <div class="form-floating col-6 mt-3">
                                                    <select class="form-select" name="etapamecanica" id="etapamecanica">
                                                        <option disabled>Seleccione la etapa</option>
                                                        <option value="1" <?= ($mecanica_actual == 1) ? 'selected' : ''; ?>>Revisión BOM mecánico</option>
                                                        <option value="2" <?= ($mecanica_actual == 2) ? 'selected' : ''; ?>>Armado de componentes mecánicos</option>
                                                        <option value="3" <?= ($mecanica_actual == 3) ? 'selected' : ''; ?>>Pruebas de ensamble</option>
                                                        <option value="4" <?= ($mecanica_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                        <option value="5" <?= ($mecanica_actual == 5) ? 'selected' : ''; ?>>Desensamble para acabados</option>
                                                        <option value="6" <?= ($mecanica_actual == 6) ? 'selected' : ''; ?>>Armado final</option>
                                                    </select>
                                                    <label for="etapamecanica">Etapa de ensamble mecánica/neumatica</label>
                                                </div>

                                                <div class="form-floating col-6 mt-3">
                                                    <select class="form-select" name="etapatcontrol" id="etapatcontrol">
                                                        <option disabled>Seleccione la etapa</option>
                                                        <option value="1" <?= ($tcontrol_actual == 1) ? 'selected' : ''; ?>>Revisión BOM controles</option>
                                                        <option value="2" <?= ($tcontrol_actual == 2) ? 'selected' : ''; ?>>Armado de tableros de control</option>
                                                        <option value="3" <?= ($tcontrol_actual == 3) ? 'selected' : ''; ?>>Pruebas electrícas y de comunicación</option>
                                                        <option value="4" <?= ($tcontrol_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                        <option value="5" <?= ($tcontrol_actual == 5) ? 'selected' : ''; ?>>Ensamble en maquinaria</option>
                                                        <option value="6" <?= ($tcontrol_actual == 6) ? 'selected' : ''; ?>>Ruteo final</option>
                                                        <option value="7" <?= ($tcontrol_actual == 7) ? 'selected' : ''; ?>>Etiquetado</option>
                                                    </select>
                                                    <label for="etapatcontrol">Etapa ensamble T.Control</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <textarea type="text" class="form-control" name="detalles" id="detalles" style="min-height:150px;"><?= $registro['detalles']; ?></textarea>
                                                    <label for="detalles">Detalles</label>
                                                </div>

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