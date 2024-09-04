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
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ensambles' WHERE `usuarios`.`codigo` = '$codigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
    } else {
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM asistencia WHERE estatus = 1 AND idcodigo = '$codigo' LIMIT 1";
$result = mysqli_query($con, $sql);

// If a matching record is found, set variables for modal content
if (mysqli_num_rows($result) > 0) {
    $registro = mysqli_fetch_assoc($result);
    $entrada = $registro['entrada'];
    $salida = $registro['salida'];
    $fecha = $registro['fecha'];

    // Convertir la hora de entrada y salida a objetos DateTime
    $entrada_dt = new DateTime($entrada);
    $salida_dt = new DateTime($salida);

    // Calcular la diferencia entre la hora de entrada y salida
    $duracion_jornada = $entrada_dt->diff($salida_dt)->format('%H:%I'); // Formato horas:minutos
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#revision').modal('show');
        });
    </script>
<?php
}
?>

<!-- Modal solicitud salida -->
<div class="modal fade" id="revision" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b>COMPLETAR HORA DE SALIDA</b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeasistencia.php" method="POST" class="row">
                    <input type="hidden" id="id" name="id" value="<?= $registro['id']; ?>">
                    <input type="hidden" id="codigo" name="codigo" value="<?= $registro['idcodigo']; ?>">
                    <div class="col-12">
                        <p class="small">Recibiste una solicitud de revisión sobre tu <b>hora de salida</b>, verifica que los datos sean correctos y si estas de acuerdo aprueba la solicitud.</p>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="text" class="form-control" id="fecha" value="<?= $fecha; ?>" placeholder="Fecha" disabled>
                        <label for="fecha">Fecha <span class="small">(YYYY/MM/DD)</span></label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input type="text" class="form-control" id="entrada" value="<?= $entrada; ?>" placeholder="Entrada" disabled>
                        <label for="entrada">Hora de entrada</label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input style="background-color:#ffdca1;" type="text" class="form-control" id="salida" value="<?= $salida; ?>" placeholder="Salida" disabled>
                        <label for="salida">Hora de salida</label>
                    </div>
                    <div class="col-12">
                        <p>Tu jornada fue de: <b><?= $duracion_jornada; ?></b> <span class="small">hrs</span></p>
                    </div>
                    <div class="modal-footer">
                        <p class="small">Tu jornada total de trabajo se calcula con el número de entradas y salidas que registres en un día, si deseas conocer el total de horas trabajadas para este día puedes consultarlo en <a href="asistenciapersonal.php?id=<?= $registro['idcodigo']; ?>">asistencia</a>.</p>
                        <button type="submit" class="btn btn-danger" name="rechazar">Rechazar</button>
                        <button type="submit" class="btn btn-success" name="aprobar">Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ensamble | Solara</title>
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
                    <div class="col-12 mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>ENSAMBLES FINALIZADOS</h4>
                                <a href="ensamble.php" class="btn btn-primary btn-sm" id="floatingButton">
                                Ensambles<br>activos
                            </a>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-bordered table-striped text-center" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Proyecto</th>
                                            <th>Diagrama / actividad asociados</th>
                                            <th>Número de piezas</th>
                                            <th>Técnicos asignados</th>
                                            <th>Prioridad</th>
                                            <th>Nivel de pieza</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT proyecto.*, diagrama.*
                                        FROM diagrama 
                                        JOIN proyecto ON diagrama.idproyecto = proyecto.id
                                        WHERE estatusplano = 0 
                                        ORDER BY proyecto.prioridad asc";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (empty($registro['medio'])) {
                                                        ?>
                                                            <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?></p>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <a href="verdiagrama.php?id=<?= $registro['id']; ?>" class="btn btn-outline-dark btn-sm">Diagrama <?= $registro['nombreplano']; ?></a>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $registro['piezas']; ?></td>
                                                    <td>
                                                        <?php
                                                        $queryAsignacion = "SELECT asignaciondiagrama.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                            FROM asignaciondiagrama
                                                            JOIN usuarios ON asignaciondiagrama.codigooperador = usuarios.codigo
                                                            WHERE asignaciondiagrama.idplano = " . $registro['id'];
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
                                                        echo "<td style='background-color:#e56f00 !important'>Nivel 2</td>";
                                                    } elseif ($registro['nivel'] === '3') {
                                                        echo "<td style='background-color:#e5da00 !important'>Nivel 3</td>";
                                                    } elseif ($registro['nivel'] === '4') {
                                                        echo "<td style='background-color:#17e500 !important'>Nivel 4</td>";
                                                    } else {
                                                        echo "<td>Error, contacte a soporte</td>";
                                                    }
                                                    ?>
                                                    <td>
                                                        <a href="editardiagrama.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='8'><p>No se encontro ningun registro</p></td>";
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTablaDos').DataTable({
                "order": [
                    [4, "asc"],
                    [5, "asc"]
                ] // Ordenar la primera columna (índice 0) en orden descendente
            });
        });
    </script>
</body>

</html>