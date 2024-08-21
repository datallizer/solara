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

//Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Historial permisos' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <title>Permisos | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-md-center justify-content-start mt-5 mb-3">
                    <div class="col-12">
                        <h2 class="mb-3">PERMISOS</h2>
                    </div>
                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="historialpermisos.php"><i class="bi bi-arrow-counterclockwise" style="font-size: 30px;"></i><br>Historico</a>
                    </div>
                    <div class="col card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="permisosrechazados.php"><i class="bi bi-x-circle-fill" style="font-size: 30px;"></i><br>Permisios rechazados</a>
                    </div>
                    <?php
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                        echo '
                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="asistencia.php"><i class="bi bi-clipboard2-check-fill" style="font-size: 30px;"></i><br>Asistencia</a>
                    </div>';
                    }
                    ?>
                </div>
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>SOLICITUDES VIGENTES
                                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo permiso
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Usuario</th>
                                            <th>Permiso</th>
                                            <th>Rol</th>
                                            <th>Fecha</th>
                                            <th>Tiempo</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                                            $query = "SELECT permisos.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.rol 
                                            FROM permisos 
                                            INNER JOIN usuarios ON permisos.idcodigo = usuarios.codigo 
                                            WHERE permisos.estatus = 1
                                            AND permisos.fecha >= CURDATE()
                                            ORDER BY permisos.id DESC;
                                            ";
                                        } else {
                                            $query = "SELECT permisos.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.rol 
                                            FROM permisos 
                                            INNER JOIN usuarios ON permisos.idcodigo = usuarios.codigo 
                                            WHERE permisos.estatus = 1
                                            AND permisos.fecha >= CURDATE() AND permisos.idcodigo = $codigo
                                            ORDER BY permisos.id DESC;
                                            ";
                                        }

                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['id']; ?></td>
                                                    <td><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></td>
                                                    <td><?= $registro['detalle']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($registro['rol'] === '1') {
                                                            echo "Administrador";
                                                        } else if ($registro['rol'] === '2') {
                                                            echo "Gerencia";
                                                        } else if ($registro['rol'] === '4') {
                                                            echo "Técnico controles";
                                                        } else if ($registro['rol'] === '5') {
                                                            echo "Ing. Diseño";
                                                        } else if ($registro['rol'] === '6') {
                                                            echo "Compras";
                                                        } else if ($registro['rol'] === '7') {
                                                            echo "Almacenista";
                                                        } else if ($registro['rol'] === '8') {
                                                            echo "Técnico mecanico";
                                                        } else if ($registro['rol'] === '9') {
                                                            echo "Ing. Control";
                                                        } else if ($registro['rol'] === '10') {
                                                            echo "Recursos humanos";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $registro['fecha']; ?></td>
                                                    <td><?= $registro['tiempo']; ?><small>hrs</small></td>
                                                    <td>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                                                            echo '
                                                            <form action="codepermisos.php" method="POST" class="d-inline">
                                                                <button type="submit" name="aprobar" value="' . $registro['id'] . '" class="btn btn-success btn-sm m-1"><i class="bi bi-check2"></i> Aprobar</button>
                                                                <button type="submit" name="rechazar" value="' . $registro['id'] . '" class="btn btn-warning btn-sm m-1"><i class="bi bi-x-lg"></i> Rechazar</button>
                                                            </form>';
                                                        }
                                                        ?>
                                                        <form action="codepermisos.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='7'><p>No hay solicitudes pendientes</p></td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="mt-5 p-3 bg-light" id='calendar'></div>
                            </div>
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PERMISO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codepermisos.php" method="POST" class="row">
                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="idcodigo" id="idcodigo" placeholder="IdCodigo" value="<?php echo $_SESSION['codigo']; ?>" readonly>
                            <label for="nombre">Código</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <select class="form-select" name="detalle" id="detalle" autocomplete="off" required>
                                <option selected disabled>Seleccione una opción</option>
                                <option value="Vacaciones">Vacaciones</option>
                                <option value="Tiempo por tiempo">Tiempo por tiempo</option>
                                <option value="Día habíl">Día habíl</option>
                                <option value="Permiso médico">Permiso médico</option>
                                <option value="Permiso por asuntos personales">Permiso por asuntos personales</option>
                                <option value="Permiso por maternidad">Permiso por maternidad</option>
                            </select>
                            <label for="detalle">Permiso</label>
                        </div>

                        <div class="form-floating col-12 col-md-6 mt-3">
                            <input type="date" class="form-control" name="fecha" id="fecha" placeholder="Fecha" autocomplete="off" required min="<?php echo date('Y-m-d'); ?>">
                            <label for="fecha">Fecha inicio</label>
                        </div>

                        <div class="form-floating col-12 col-md-6 mt-3">
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha fin" autocomplete="off" required min="<?php echo date('Y-m-d'); ?>">
                            <label for="fecha_fin">Fecha fin</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mb-3">
                            <input type="time" class="form-control" name="tiempo" id="tiempo" placeholder="Tiempo" autocomplete="off" required>
                            <label for="tiempo">Tiempo</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 100
            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // Obtener referencia a los campos de fecha de inicio y fin
            const fechaInicio = document.getElementById('fecha');
            const fechaFin = document.getElementById('fecha_fin');

            // Añadir evento change al campo de fecha de inicio
            fechaInicio.addEventListener('change', function() {
                // Obtener valor del campo de fecha de inicio
                const fechaInicioValue = fechaInicio.value;

                // Asignar el mismo valor al campo de fecha de fin
                fechaFin.value = fechaInicioValue;
            });

            const detalleSelect = document.getElementById('detalle');
            const tiempoInput = document.getElementById('tiempo');

            detalleSelect.addEventListener('change', function() {
                // Verificar si la opción seleccionada es "Vacaciones"
                if (detalleSelect.value === 'Vacaciones') {
                    // Establecer el valor del campo de tiempo en "08:00"
                    tiempoInput.value = '08:00';
                    // Hacer el campo de tiempo de solo lectura
                    tiempoInput.setAttribute('readonly', 'readonly');
                } else {
                    // Eliminar el atributo readonly y restablecer el valor predeterminado
                    tiempoInput.removeAttribute('readonly');
                    tiempoInput.value = ''; // Aquí puedes establecer el valor predeterminado si lo deseas
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    <?php

                    $query = "SELECT permisos.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.rol 
                     FROM permisos 
                     INNER JOIN usuarios ON permisos.idcodigo = usuarios.codigo WHERE permisos.estatus = 2 and permisos.idcodigo = $codigo
                     ORDER BY permisos.id DESC";
                    $result = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Formatear la fecha en el formato ISO-8601 (YYYY-MM-DD)
                        $formattedDate = date('Y-m-d', strtotime($row['fecha']));

                        echo "{";
                        echo "title: '{$row['detalle']}',";
                        echo "start: '{$formattedDate}',"; // Utilizar la fecha formateada
                        echo "},";
                    }
                    ?>
                ]
            });

            calendar.render();
        });
    </script>
</body>

</html>