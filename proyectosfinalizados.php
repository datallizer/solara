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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Proyectos' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Proyectos | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-4">
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>PROYECTOS FINALIZADOS</h4>
                                <a href="proyectos.php" class="btn btn-primary btn-sm" id="floatingButton">
                                Proyectos<br>activos
                            </a>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Cliente</th>
                                            <th>Otros datos</th>
                                            <th>Prioridad</th>
                                            <th>Etapa diseño</th>
                                            <th>Etapa control</th>
                                            <th>Detalles</th>
                                            <th>Encargado(s) de proyecto</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [5, 9])) {
                                            $query = "SELECT proyecto.*
                                                FROM proyecto 
                                                JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
                                                JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo
                                                WHERE encargadoproyecto.codigooperador = $codigo 
                                                AND proyecto.estatus = 0
                                                ORDER BY proyecto.prioridad ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                            $query = "SELECT * FROM proyecto WHERE estatus = 0 ORDER BY prioridad ASC";
                                        }
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p class="text-center"><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-center"><?= $registro['nombre']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-center"><?= $registro['cliente']; ?></p>
                                                    </td>
                                                    <td style="min-width: 250px;">
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                            echo '<p><b>Presupuesto: </b>$' . $registro['presupuesto'] . '</p>';
                                                        }
                                                        ?>
                                                        <p><b>Fecha de inicio:</b> <?= $registro['fechainicio']; ?></p>
                                                        <p><b>Fecha finalización:</b> <?= $registro['fechafin']; ?></p>
                                                    </td>
                                                    <?php
                                                    if ($registro['prioridad'] == 1) {
                                                        echo "<td style='background-color: #ff0000;color:#fff;'>" . $registro['prioridad'] . "</td>"; // Rojo oscuro
                                                    } elseif ($registro['prioridad'] == 2) {
                                                        echo "<td style='background-color: #ff1a1a;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 3) {
                                                        echo "<td style='background-color: #ff3333;'>" . $registro['prioridad'] . "</td>"; // Rojo medio
                                                    } elseif ($registro['prioridad'] == 4) {
                                                        echo "<td style='background-color: #ff4d4d;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 5) {
                                                        echo "<td style='background-color: #ff6666;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 6) {
                                                        echo "<td style='background-color: #ff8080;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 7) {
                                                        echo "<td style='background-color: #ff9999;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 8) {
                                                        echo "<td style='background-color: #ffb2b2;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 9) {
                                                        echo "<td style='background-color: #ffcccc;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 10) {
                                                        echo "<td style='background-color: #ffe5e5;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                                    } elseif ($registro['prioridad'] == 11) {
                                                        echo "<td style='background-color: #ffffb3;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 12) {
                                                        echo "<td style='background-color: #ffff99;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 13) {
                                                        echo "<td style='background-color: #ffff80;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 14) {
                                                        echo "<td style='background-color: #ffff66;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 15) {
                                                        echo "<td style='background-color: #ffff4d;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 16) {
                                                        echo "<td style='background-color: #ffff33;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 17) {
                                                        echo "<td style='background-color: #ffff1a;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 18) {
                                                        echo "<td style='background-color: #ffff00;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 19) {
                                                        echo "<td style='background-color: #ffff00;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 20) {
                                                        echo "<td style='background-color: #e5e500;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                                    } elseif ($registro['prioridad'] == 21) {
                                                        echo "<td style='background-color: #c6e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 22) {
                                                        echo "<td style='background-color: #a8e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 23) {
                                                        echo "<td style='background-color: #89e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 24) {
                                                        echo "<td style='background-color: #67e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 25) {
                                                        echo "<td style='background-color: #58e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 26) {
                                                        echo "<td style='background-color: #39e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 27) {
                                                        echo "<td style='background-color: #26e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 28) {
                                                        echo "<td style='background-color: #00e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 29) {
                                                        echo "<td style='background-color: #00e51b;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } elseif ($registro['prioridad'] == 30) {
                                                        echo "<td style='background-color: #00e539;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                                    } else {
                                                        echo "<td>" . $registro['prioridad'] . "</td>"; // Valor fuera del rango
                                                    }
                                                    ?>

                                                    <td>
                                                        <p><?php
                                                            if ($registro['etapadiseño'] === '1') {
                                                                echo "Diseño";
                                                            } else if ($registro['etapadiseño'] === '2') {
                                                                echo "Revisión interna";
                                                            } else if ($registro['etapadiseño'] === '3') {
                                                                echo "Revisión con cliente";
                                                            } else if ($registro['etapadiseño'] === '4') {
                                                                echo "Planos";
                                                            } else if ($registro['etapadiseño'] === '5') {
                                                                echo "Bom";
                                                            } else if ($registro['etapadiseño'] === '6') {
                                                                echo "Manufactura";
                                                            } else if ($registro['etapadiseño'] === '7') {
                                                                echo "Remediación";
                                                            } else if ($registro['etapadiseño'] === '8') {
                                                                echo "Documentación";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                    </td>
                                                    <td style="cursor: all-scroll;">
                                                        <p><?php
                                                            if ($registro['etapacontrol'] === '1') {
                                                                echo "Diseño";
                                                            } else if ($registro['etapacontrol'] === '2') {
                                                                echo "Revisión interna";
                                                            } else if ($registro['etapacontrol'] === '3') {
                                                                echo "Revisión con cliente";
                                                            } else if ($registro['etapacontrol'] === '4') {
                                                                echo "Diagramas";
                                                            } else if ($registro['etapacontrol'] === '5') {
                                                                echo "Bom";
                                                            } else if ($registro['etapacontrol'] === '6') {
                                                                echo "Manufactura";
                                                            } else if ($registro['etapacontrol'] === '7') {
                                                                echo "Programación";
                                                            } else if ($registro['etapacontrol'] === '8') {
                                                                echo "Debugging";
                                                            } else if ($registro['etapacontrol'] === '9') {
                                                                echo "Documentación";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['detalles']; ?></p>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $queryAsignacion = "SELECT encargadoproyecto.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom
                                                        FROM encargadoproyecto
                                                        JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo
                                                        WHERE encargadoproyecto.idproyecto = " . $registro['id'];

                                                        $query_run_asignacion = mysqli_query($con, $queryAsignacion);

                                                        if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                            foreach ($query_run_asignacion as $asignacion) {
                                                                echo '<p>' . $asignacion['nombre'] . ' ' . $asignacion['apellidop'] . ' ' . $asignacion['apellidom'] . '</p>';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="editarproyecto.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='10'><p>No se encontro ningun registro</p></td>";
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
</body>

</html>