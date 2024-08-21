<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
header('Content-Type: text/html; charset=UTF-8');
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión
$codigo = $_SESSION['codigo'];
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
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['message']);
}
if (isset($_SESSION['codigo'])) {
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Maquinados' WHERE `usuarios`.`codigo` = '$codigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
    } else {
        header('Location: login.php');
        exit();
    }
} else {
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
    <title>Maquinados finalizados | Solara</title>
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
                <a href="maquinados.php" class="btn btn-primary btn-sm" id="floatingButton">
                    Maquinados<br>activos
                </a>
                <div class="row mb-5 mt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>MAQUINADOS FINALIZADOS</h4>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Plano / actividad asociados</th>
                                            <th>Número de piezas</th>
                                            <th>Operadores asignados</th>
                                            <th>Prioridad</th>
                                            <th>Nivel de pieza</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT proyecto.*, plano.*, plano.id AS idplano
                                        FROM plano 
                                        JOIN proyecto ON plano.idproyecto = proyecto.id
                                        WHERE estatusplano = 0 
                                        ORDER BY idplano DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['idplano']; ?></td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <?php
                                                        // Verifica si 'medio' está vacío o no
                                                        if (empty($registro['medio'])) {
                                                        ?>
                                                            <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?></p>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <a href="verplano.php?id=<?= $registro['id']; ?>" class="btn btn-outline-dark btn-sm">Plano <?= $registro['nombreplano']; ?></a>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center"><?= $registro['piezas']; ?></td>
                                                    <td>
                                                        <?php
                                                        $queryAsignacion = "SELECT asignacionplano.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                            FROM asignacionplano
                                                            JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                                            WHERE asignacionplano.idplano = " . $registro['id'];
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
                                                    <td class="text-center"><?= $registro['prioridad']; ?></td>
                                                    <?php
                                                    if ($registro['nivel'] === '1') {
                                                        echo "<td style='background-color:#e50000 !important;color:#fff;'>Nivel 1</td>";
                                                    } elseif ($registro['nivel'] === '2') {
                                                        echo "<td style='background-color:#e56f00 !important;color:#fff;'>Nivel 2</td>";
                                                    } elseif ($registro['nivel'] === '3') {
                                                        echo "<td style='background-color:#e5da00 !important'>Nivel 3</td>";
                                                    } elseif ($registro['nivel'] === '4') {
                                                        echo "<td style='background-color:#17e500 !important'>Nivel 4</td>";
                                                    } else {
                                                        echo "<td>Error, contacte a soporte</td>";
                                                    }
                                                    ?>
                                                    <td>
                                                        <a href="editarmaquinado.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='7'><p>No se encontro ningun registro</p></td>";
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
    <!-- Incluir los archivos de PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTablaDos').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
</body>

</html>