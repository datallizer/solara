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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Ingenieria' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Ingeniería | Solara</title>
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
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>INGENIERÍA ACTIVIDADES ASIGNADAS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Nueva actividad
                                </button> 
                                <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalAsignar">
                                    Asignar ingeniero
                                </button>
                                <a href="ingenieriafinalizados.php" class="btn btn-primary btn-sm" id="floatingButton">
                                Ingeniería<br>finalizados
                            </a>';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Actividad</th>
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                echo '<th>Ingeníeros asignados</th>';
                                            }
                                            ?>
                                            <th>Prioridad</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [5, 9, 13])) {
                                            $query = "SELECT proyecto.id,proyecto.nombre, ingenieria.*
                                            FROM ingenieria 
                                            JOIN proyecto ON ingenieria.idproyecto = proyecto.id 
                                            JOIN asignacioningenieria ON asignacioningenieria.idplano = ingenieria.id 
                                            JOIN usuarios ON asignacioningenieria.codigooperador = usuarios.codigo
                                            WHERE asignacioningenieria.codigooperador = $codigo 
                                            AND ingenieria.estatusplano = 1 ORDER BY ingenieria.prioridad ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                            $query = "SELECT proyecto.id, proyecto.nombre, ingenieria.*
                                            FROM ingenieria 
                                            JOIN proyecto ON ingenieria.idproyecto = proyecto.id
                                            WHERE ingenieria.estatusplano = 1";
                                        }
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['id']; ?></td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?> </p>
                                                    </td>
                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                    ?>
                                                        <td>
                                                            <?php
                                                            $queryAsignacion = "SELECT asignacioningenieria.*, asignacioningenieria.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                        FROM asignacioningenieria
                                                        JOIN usuarios ON asignacioningenieria.codigooperador = usuarios.codigo
                                                        WHERE asignacioningenieria.idplano = " . $registro['id'];
                                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                                foreach ($query_run_asignacion as $asignacion) {
                                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {

                                                                        if ($registro['estatusplano'] === '1') {
                                                            ?>
                                                                            <form class="deleteForm" action="codencargados.php" method="post">
                                                                                <div style="display: flex; align-items: center;">
                                                                                    <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                                                    <button type="button" class="deleteButton" name="deleteingeniero" style="border: none;" class="btn btn-sm" data-id="<?= $asignacion['id_encargado']; ?>">
                                                                                        <i style="color: #d41111;" class="bi bi-x-lg"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                                        <?php
                                                                        }
                                                                    } else if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8])) {
                                                                        ?>
                                                                        <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                            <?php
                                                                    }
                                                                }
                                                            } else {
                                                                echo '-';
                                                            }

                                                            ?>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($registro['prioridad'] === '1') {
                                                        echo "<td style='background-color:#e50000 !important;color:#fff;'>Muy alta</td>";
                                                    } elseif ($registro['prioridad'] === '2') {
                                                        echo "<td style='background-color:#e56f00 !important;color:#fff;'>Alta</td>";
                                                    } elseif ($registro['prioridad'] === '3') {
                                                        echo "<td style='background-color:#e5da00 !important'>Normal</td>";
                                                    } elseif ($registro['prioridad'] === '4') {
                                                        echo "<td style='background-color:#17e500 !important'>Baja</td>";
                                                    } else {
                                                        echo "<td>Error, contacte a soporte</td>";
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if ($_SESSION['rol'] === '1' || $_SESSION['rol'] === '2') {
                                                        ?>
                                                            <a href="editarmaquinado.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                            <form action="codeingenieria.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                            </form>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <form action="codeingenieria.php" method="POST" class="d-inline">
                                                                <button type="submit" name="finalizar" value="<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-check2"></i> Finalizar</button>
                                                            </form>
                                                        <?php
                                                        }
                                                        ?>
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
    </div>
    <!-- Modal ingenieros-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tituloPlano">NUEVA ACTIVIDAD</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeingenieria.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['nombre'];
                                        $idProyecto = $proyecto['id'];
                                        echo "<option value='$idProyecto' " . ($registro['id'] == $idProyecto ? 'selected' : '') . ">$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idproyecto">Proyecto asociado</label>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano" id="nombrePlano">Nombre de la actividad</label>
                        </div>


                        <div class="form-floating col-12 mt-3" id="actividadElements">
                            <textarea type="text" class="form-control" name="actividad" id="actividad" placeholder="Actividad" autocomplete="off" style="min-height: 150px;"></textarea>
                            <label for="actividad">Detalles de la actividad</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <select class="form-select" name="prioridad" id="prioridad" autocomplete="off" required>
                                <option selected disabled>Seleccione una opción</option>
                                <option value="1">Muy alta</option>
                                <option value="2">Alta</option>
                                <option value="3">Normal</option>
                                <option value="4">Baja</option>
                            </select>
                            <label for="nivel">Prioridad</label>
                        </div>

                        <div class="form-check col-12 mt-3 m-3">
                            <?php
                            $query = "SELECT * FROM usuarios WHERE rol = 5 OR rol = 9 OR rol = 13 AND estatus = 1";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($usuario = mysqli_fetch_assoc($result)) {
                                    $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                    $idUsuario = $usuario['codigo'];
                                    echo "<input class='form-check-input' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                    echo "<label class='form-check-label' for='codigooperador_$idUsuario'>$nombreCompleto</label><br>";
                                }
                            }
                            ?>
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

    <!-- Modal Operadores -->
    <div class="modal fade" id="exampleModalAsignar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR INGENIERO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codetecnicos.php" method="POST" class="row">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="idplano" id="idplano">
                                <option disabled selected>Seleccione una actividad</option>
                                <?php
                                $query = "SELECT * FROM ingenieria WHERE estatusplano = 1";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($plano = mysqli_fetch_assoc($result)) {
                                        $opcion = $plano['nombreplano'];
                                        $idPlano = $plano['id'];

                                        $queryAsignacion = "SELECT COUNT(*) as count FROM asignacioningenieria WHERE idplano = ?";
                                        $stmt = $con->prepare($queryAsignacion);
                                        $stmt->bind_param("i", $idPlano);
                                        $stmt->execute();
                                        $resultAsignacion = $stmt->get_result();
                                        $row = $resultAsignacion->fetch_assoc();

                                        if ($row['count'] > 0) {
                                            echo "<option value='$idPlano'>" . htmlspecialchars($opcion) . " - Asignado</option>";
                                        } else {
                                            echo "<option value='$idPlano'>" . htmlspecialchars($opcion) . " - No asignado</option>";
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <label for="idplano">Plano a asociar</label>
                        </div>

                        <div class="form-check col-12 mt-3 m-3" id="usuariosContainer">
                            <?php
                            $query = "SELECT * FROM usuarios WHERE rol = 5 OR rol = 9 OR rol = 13 AND estatus = 1";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($usuario = mysqli_fetch_assoc($result)) {
                                    $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                    $idUsuario = $usuario['codigo'];

                                    echo "<input class='form-check-input' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                    echo "<label class='form-check-label' for='codigooperador_$idUsuario'>$nombreCompleto</label><br>";
                                }
                            }
                            ?>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="ingeniero">Guardar</button>
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
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25
            });

            // Cambiar a usar clase en lugar de ID
            $('.deleteButton').on('click', function(event) {
                event.preventDefault(); // Previene el envío del formulario por defecto

                const form = $(this).closest('form'); // Encuentra el formulario más cercano al botón
                const deleteValue = $(this).data('id'); // Obtiene el valor del data-id del botón

                Swal.fire({
                    title: 'ADVERTENCIA',
                    text: '¿Estás seguro que deseas eliminar la asignación de la actividad al usuario actual? Deberás asignar un usuario nuevo.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Añadir un campo oculto con el valor del botón al formulario
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'deleteingeniero',
                            value: deleteValue
                        }).appendTo(form);

                        // Si el usuario confirma, se envía el formulario
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>