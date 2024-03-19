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
        // El usuario está autorizado, se puede acceder al contenido
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
    <title>Usuarios | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
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
                    <div class="col-12">
                    <h3 class="p-2" style="text-transform: uppercase;">registro de
                                    <?php
                                    if (isset($_GET['id'])) {
                                        $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                        $query = "SELECT * FROM usuarios WHERE codigo='$registro_id' ";
                                        $query_run = mysqli_query($con, $query);

                                        if (mysqli_num_rows($query_run) > 0) {
                                            $registro = mysqli_fetch_array($query_run);
                                            $nombre = $registro['nombre'];
                                            $apellidop = $registro['apellidop'];
                                            $apellidom = $registro['apellidom'];
                                            echo $nombre . ' ' . $apellidop . ' ' . $apellidom;
                                        }
                                    }
                                    ?>

                                    <a href="asistencia.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                </h3>
                    </div>
                <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">ASISTENCIA GENERAL</h4>
                            </div>
                            <div class="card-body">
                                <table id="miTabla" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Codigo</th>
                                            <th scope="col">Entrada</th>
                                            <th scope="col">Salida</th>
                                            <th scope="col">Jornada</th>
                                            <th scope="col">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
$registro_id = mysqli_real_escape_string($con, $_GET['id']);
$query = "SELECT fecha, 
                 MIN(entrada) AS entrada_earliest, 
                 MAX(salida) AS salida_latest, 
                 TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(salida, entrada)))), '%H:%i') AS horas_trabajadas_total
          FROM asistencia 
          WHERE idcodigo = $registro_id
          GROUP BY fecha";
$query_run = mysqli_query($con, $query);
if (mysqli_num_rows($query_run) > 0) {
    $index = 1;
    foreach ($query_run as $registro) {
        ?>
        <tr>
            <td>
                <p><?= $index++; ?></p>
            </td>
            <td>
                <p><?= $registro_id; ?></p>
            </td>
            <td>
                <p><?= $registro['entrada_earliest']; ?></p>
            </td>
            <td>
                <p><?= $registro['salida_latest']; ?></p>
            </td>
            <td>
                <p><?= $registro['horas_trabajadas_total']; ?> <small>hrs</small></p>
            </td>
            <td>
                <p><?= $registro['fecha']; ?></p>
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">ASISTENCIA DETALLADA</h4>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Codigo</th>
                                            <th scope="col">Entrada</th>
                                            <th scope="col">Salida</th>
                                            <th scope="col">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $registro_id = mysqli_real_escape_string($con, $_GET['id']);


                                        $query = "SELECT * FROM asistencia WHERE idcodigo = $registro_id";

                                        $query_run = mysqli_query($con, $query);

                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['idcodigo']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['entrada']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['salida']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['fecha']; ?></p>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'><p>No se encontró ningún registro</p></td></tr>";
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
        </script>
</body>

</html>