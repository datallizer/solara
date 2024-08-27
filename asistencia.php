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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Asistencia' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <title>Asistencia | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-evenly mt-5 mb-5">
                    <div class="col-12">
                        <h2 class="mb-3">ASISTENCIA</h2>
                    </div>
                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="asistenciageneral.php"><i class="bi bi-grid-3x3-gap-fill" style="font-size: 30px;"></i><br>General</a>
                    </div>
                    <?php
                    // Suponiendo que ya tienes una conexión a tu base de datos
                    // Realiza la consulta SQL para obtener el número de registros con estatus = 2
                    $sql = "SELECT COUNT(*) AS total_registros FROM asistencia WHERE estatus = 2";
                    $resultado = mysqli_query($con, $sql);
                    $fila = mysqli_fetch_assoc($resultado);
                    $total_registros = $fila['total_registros'];
                    ?>

                    <div class="col card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="solicitudesrechazadasasistencia.php"><i class="bi bi-x-circle-fill" style="font-size: 30px;"></i><br>Solicitudes rechazadas</a>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $total_registros; ?>
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </div>

                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="permisos.php"><i class="bi bi-calendar-check-fill" style="font-size: 30px;"></i><br>Permisos</a>
                    </div>
                    <div class="col-12 p-3 text-center mt-3" style="border: 1px solid #e7e7e7;">
                        <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM usuarios WHERE estatus=1 AND rol<> 1 AND rol <> 12 ORDER BY id DESC";
                                $query_run = mysqli_query($con, $query);
                                if (mysqli_num_rows($query_run) > 0) {
                                    foreach ($query_run as $registro) {
                                ?>
                                        <tr class="text-start">
                                            <td><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></td>
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
                                                } else if ($registro['rol'] === '13') {
                                                    echo "Ing. Laser";
                                                } else {
                                                    echo "Error, contacte a soporte";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="asistenciapersonal.php?id=<?= $registro['codigo']; ?>" class="btn btn-primary btn-sm m-1"><i class="bi bi-eye-fill"></i></a>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<td colspan='4'><p>No se encontro ningun usuario</p></td>";
                                }
                                ?>
                            </tbody>
                        </table>
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
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25
            });
        });
    </script>
</body>

</html>