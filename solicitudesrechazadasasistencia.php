<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión
$codigo = $_SESSION['codigo'];
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

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Solicitudes de asistencia rechazadas' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Asistencia global | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
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
                    <div class="col-12">
                        <h3 class="p-2 bg-dark text-light align-items-top" style="text-transform: uppercase;border-radius:5px;">
                            SOLICITUDES RECHAZADAS
                            <a href="asistencia.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                        </h3>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">REGISTRO DETALLADO</h4>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Entrada</th>
                                            <th scope="col">Salida rechazada</th>
                                            <th scope="col">Empleado</th>
                                            <th scope="col">Fecha</th>
                                            <th scope="col">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $fecha_actual = date('Y-m-d'); // Obtiene la fecha actual en formato 'YYYY-MM-DD'

                                        $query = "SELECT asistencia.*, usuarios.nombre, usuarios.apellidop FROM asistencia INNER JOIN usuarios ON asistencia.idcodigo = usuarios.codigo WHERE asistencia.estatus = 2";
                                        $query_run = mysqli_query($con, $query);

                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['entrada']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['salida']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['fecha']; ?></p>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $registro['id']; ?>">Completar</button>
                                                        <!-- Modal para solicitud de salida -->
                                                        <div class="modal fade" id="exampleModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?= $registro['id']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" id="exampleModalLabel<?= $registro['id']; ?>">ACTUALIZAR HORA DE SALIDA</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form id="asistenciaForm" action="codeasistencia.php" method="POST" class="row">
                                                                            <input type="hidden" id="id" name="id" value="<?= $registro['id']; ?>">
                                                                            <input type="hidden" id="codigo" name="codigo" value="<?= $registro['idcodigo']; ?>">
                                                                            <div class="form-floating col-12 mb-3">
                                                                                <input type="text" class="form-control" id="fecha" value="<?= $registro['fecha']; ?>" placeholder="Fecha" autocomplete="off" disabled>
                                                                                <label for="fecha">Fecha <span class="small">(YYYY/MM/DD)</span></label>
                                                                            </div>
                                                                            <div class="form-floating col-12 mb-3">
                                                                                <input type="time" class="form-control" id="entrada" value="<?= $registro['entrada']; ?>" placeholder="Entrada" autocomplete="off" disabled>
                                                                                <label for="entrada">Hora de entrada</label>
                                                                            </div>
                                                                            <div class="form-floating col-12 mb-3">
                                                                                <input type="time" class="form-control" id="salidarechazada" value="<?= $registro['salida']; ?>" placeholder="Salida rechazada" autocomplete="off" disabled>
                                                                                <label for="salidarechazada">Hora de salida rechazada</label>
                                                                            </div>
                                                                            <div class="form-floating col-12 mb-3">
                                                                                <input style="background-color:#ffffff;" type="time" class="form-control" name="salida" id="salida" placeholder="Salida" autocomplete="off" required>
                                                                                <label for="salida">Nueva hora de salida</label>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <p id="duracionJornada">La jornada será de: </p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                                <button type="submit" class="btn btn-primary" name="solicitar">Solicitar</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
        <script>
            $(document).ready(function() {
                $('#miTabla, #miTablaDos').DataTable({
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 25
                });
            });

            document.getElementById("asistenciaForm").addEventListener("submit", function(event) {
                // Obtener la hora de entrada y salida
                var entrada = new Date("<?= $registro['fecha'] ?> " + document.getElementById("entrada").value);
                var salida = new Date("<?= $registro['fecha'] ?> " + document.getElementById("salida").value);

                // Verificar si la hora de salida es anterior a la hora de entrada
                if (salida <= entrada) {
                    // Mostrar un mensaje de error con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '¡La hora de salida no puede ser anterior o igual a la hora de entrada!'
                    });
                    // Prevenir el envío del formulario
                    event.preventDefault();
                }
            });

            var salidaInput = document.getElementById("salida");
            salidaInput.addEventListener("input", function() {
                // Obtener la hora de entrada y salida
                var entrada = new Date("<?= $registro['fecha'] ?> " + document.getElementById("entrada").value);
                var salida = new Date("<?= $registro['fecha'] ?> " + this.value);

                // Calcular la diferencia en milisegundos
                var diferencia = salida.getTime() - entrada.getTime();

                // Convertir la diferencia de milisegundos a horas y minutos
                var horas = Math.floor(diferencia / (1000 * 60 * 60));
                var minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));

                // Mostrar la duración de la jornada en el párrafo
                document.getElementById("duracionJornada").textContent = "La jornada será de: " + horas + " horas y " + minutos + " minutos";
            });
        </script>
</body>

</html>