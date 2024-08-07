<?php
session_start();
require 'dbcon.php';


// Verificar si hay una sesión activa y si los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Monitor de sesiones' WHERE `usuarios`.`codigo` = '$codigo'";
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

// Lógica para manejar las actualizaciones de las sesiones en tiempo real
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_sessions') {
    $response = array();

    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "SELECT u.nombre, u.apellidop, u.apellidom, u.rol, u.estatus, u.ubicacion,
          (CASE WHEN EXISTS (
              SELECT 1 
              FROM asistencia a 
              WHERE a.idcodigo = u.codigo 
              AND DATE(a.fecha) = '$fecha_actual' 
              AND TIME(a.fecha) <= '$hora_actual' 
              AND a.salida IS NULL
              AND u.ubicacion <> 'Fin de jornada laboral'
          ) THEN '1' ELSE '0' END) AS activo
          FROM usuarios u
          WHERE u.estatus = 1 AND rol <> 1 AND rol <> 2
          ORDER BY u.id DESC";



    $result = mysqli_query($con, $query);

    if (!$result) {
        // Manejo de errores en caso de fallo en la consulta
        echo "Error en la consulta: " . mysqli_error($con);
        exit(); // Finalizar el script en caso de error
    }

    if (mysqli_num_rows($result) > 0) {
        $response['sessions'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $response['sessions'] = array();
    }

    echo json_encode($response); // Imprimir el resultado de la consulta para verificar si hay datos
    exit(); // Finalizar el script después de devolver la respuesta JSON
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
    <title>Monitor | Solara</title>
</head>

<body class="sb-nav-fixed">
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-evenly mt-5 mb-5">
                    <div class="col-12">
                        <h2 class="mb-3">MONITOR DE SESIONES</h2>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>SESIONES ACTIVAS</h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Ubicación</th>
                                            <th>Sesión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>SESIONES INACTIVAS</h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Ubicación</th>
                                            <th>Sesión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script> -->
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            function getSessions() {
                $.ajax({
                    url: 'sesiones.php?action=get_sessions',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#miTabla tbody').empty();
                        $('#miTablaDos tbody').empty();

                        response.sessions.forEach(function(session) {
                            var sessionStatus = session.activo === '1' ? '<i style="color:#09e83a;" class="bi bi-circle-fill"></i> Activo' : '<i style="color:#e80909;" class="bi bi-circle-fill"></i> Inactivo';
                            var sessionRow = '<tr>' +
                                '<td>' + session.nombre + ' ' + session.apellidop + ' ' + session.apellidom + '</td>' +
                                '<td>' + getRoleName(session.rol) + '</td>' +
                                '<td>' + session.ubicacion + '</td>' +
                                '<td>' + sessionStatus + '</td>' +
                                '</tr>';

                            if (session.activo === '1') {
                                $('#miTabla tbody').append(sessionRow);
                            } else {
                                $('#miTablaDos tbody').append(sessionRow);
                            }
                        });
                    }
                });
            }



            getSessions();

            // Inicializar DataTables para las tablas una vez que los datos estén presentes
            // $('#miTabla').DataTable();
            // $('#miTablaDos').DataTable();

            setInterval(getSessions, 5000);

            function getRoleName($roleId) {
                switch ($roleId) {
                    case '1':
                        return "Administrador";
                    case '2':
                        return "Gerencia";
                    case '4':
                        return "Técnico controles";
                    case '5':
                        return "Ing. Diseño";
                    case '6':
                        return "Compras";
                    case '7':
                        return "Almacenista";
                    case '8':
                        return "Técnico mecanico";
                    case '9':
                        return "Ing. Control";
                    case '10':
                        return "Recursos humanos";
                    default:
                        return "Error, contacte a soporte";
                }
            }
        });
    </script>
</body>

</html>