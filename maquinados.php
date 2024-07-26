<?php
session_start();
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
    <title>Maquinados | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>MAQUINADOS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Nuevo plano
                                </button> <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalAsignar">
                                    Asignar operador
                                </button>';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Proyecto</th>
                                            <th>Plano / actividad asociados</th>
                                            <th>Operadores asignados</th>
                                            <th>Número de piezas</th>
                                            <th>Prioridad</th>
                                            <th>Nivel de pieza</th>
                                            <th>Estatus</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8])) {
                                            $query = "SELECT proyecto.*, plano.*
                  FROM plano 
                  JOIN proyecto ON plano.idproyecto = proyecto.id 
                  JOIN asignacionplano ON asignacionplano.idplano = plano.id 
                  JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                  WHERE asignacionplano.codigooperador = $codigo 
                  AND (plano.estatusplano = 1 OR plano.estatusplano = 2 OR plano.estatusplano = 3)
                  ORDER BY proyecto.prioridad ASC, plano.nivel ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                            $query = "SELECT proyecto.*, plano.*
                  FROM plano 
                  JOIN proyecto ON plano.idproyecto = proyecto.id
                  WHERE (plano.estatusplano = 1 OR plano.estatusplano = 2 OR plano.estatusplano = 3)
                  ORDER BY proyecto.prioridad asc";
                                        }
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            $habilitarBoton = true; // Bandera para habilitar el botón
                                            $prevPrioridad = null;
                                            $prevNivel = null;
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8])) {
                                                            // Verifica si 'medio' está vacío o no
                                                            if (empty($registro['medio'])) {
                                                        ?>
                                                                <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?> </p>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <button style="min-width: 200px;" type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>"><i class="bi bi-file-pdf"></i> Plano <?= $registro['nombreplano']; ?></button>
                                                                <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="pdfModalLabel"><?= $registro['nombreplano']; ?></h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <iframe src="data:application/pdf;base64,<?= base64_encode($registro['medio']); ?>" width="100%" height="600px"></iframe>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php
                                                            }
                                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {

                                                            // Verifica si 'medio' está vacío o no
                                                            if (empty($registro['medio'])) {
                                                            ?>
                                                                <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?></p>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <a style="min-width: 200px;" href="verplano.php?id=<?= $registro['id']; ?>" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-pdf"></i> Plano <?= $registro['nombreplano']; ?></a>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $queryAsignacion = "SELECT asignacionplano.*, asignacionplano.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                        FROM asignacionplano
                                        JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                        WHERE asignacionplano.idplano = " . $registro['id'];
                                                        $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                                        if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                            foreach ($query_run_asignacion as $asignacion) {

                                                                if ($registro['estatusplano'] === '1') {
                                                        ?>
                                                                    <form action="codencargados.php" method="post">
                                                                        <div style="display: flex; align-items: center;">
                                                                            <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                                            <button type="submit" name="deleteplano" style="border: none;" class="btn btn-sm" value="<?= $asignacion['id_encargado']; ?>">
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
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center"><?= $registro['piezas']; ?></td>
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
                                                    <?php
                                                    $plano_id = $registro['id'];
                                                    $query = "SELECT COUNT(*) as count FROM asignacionplano WHERE idplano = ?";
                                                    $stmt = $con->prepare($query);
                                                    $stmt->bind_param("i", $plano_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    $row = $result->fetch_assoc();

                                                    if ($registro['estatusplano'] === '1') {
                                                        if ($row['count'] > 0) {
                                                            echo "<td>Asignado</td>";
                                                        } else {
                                                            echo "<td>No asignado</td>";
                                                        }
                                                    } elseif ($registro['estatusplano'] === '2') {
                                                        echo "<td>Pausado</td>";
                                                    } elseif ($registro['estatusplano'] === '3') {
                                                        echo "<td style='background-color:#e5da00 !important'>En progreso</td>";
                                                    } else {
                                                        echo "<td>Error, contacte a soporte</td>";
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8])) {
                                                            $countQuery = "SELECT COUNT(*) as count
                                        FROM plano
                                        JOIN asignacionplano ON asignacionplano.idplano = plano.id
                                        WHERE asignacionplano.codigooperador = $codigo AND plano.estatusplano = 3";
                                                            $countResult = mysqli_query($con, $countQuery);
                                                            $countRow = mysqli_fetch_assoc($countResult);
                                                            $planosConEstatus3 = $countRow['count'];
                                                            $id = $registro['id'];
                                                            if ($planosConEstatus3 >= 1) {
                                                                if ($registro['estatusplano'] === '3') {
                                                                    echo '<form action="codeactividad.php" method="post">
                                                                                <input type="hidden" value="' . $id . '" name="id">
                                                                                <button style="min-width:105px;" type="submit" name="restart" class="btn btn-sm btn-danger"><i class="bi bi-arrow-clockwise"></i> Reiniciar</button>
                                                                            </form>';
                                                                } else {
                                                                    echo '<button style="min-width:105px;" type="submit" class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-ban"></i> Bloqueado</button>';
                                                                }
                                                            } else {
                                                                if ($registro['estatusplano'] === '1') {
                                                                    $prioridad = $registro['prioridad'];
                                                                    $nivel = $registro['nivel'];

                                                                    if ($habilitarBoton && ($prevPrioridad === null || ($prioridad == $prevPrioridad && $nivel == $prevNivel))) {
                                                                        $botonTexto = '<i class="bi bi-play-fill"></i> Iniciar';
                                                                        $botonClase = 'btn-success';
                                                                        $botonEstado = '';
                                                                        $prevPrioridad = $prioridad;
                                                                        $prevNivel = $nivel;
                                                                    } else {
                                                                        $botonTexto = '<i class="bi bi-ban"></i> Bloqueado';
                                                                        $botonClase = 'btn-outline-success';
                                                                        $botonEstado = 'disabled';
                                                                        $habilitarBoton = false;
                                                                    }

                                                                    echo '<form action="codeactividad.php" method="post">
                  <input type="hidden" value="' . $registro['id'] . '" name="id">
                  <button style="min-width: 105px;" type="submit" name="start" class="btn btn-sm ' . $botonClase . '" ' . $botonEstado . '>' . $botonTexto . '</button>
                  </form>';
                                                                } else if ($registro['estatusplano'] === '2') {
                                                                    echo '<form action="codeactividad.php" method="post">
                                                                    <input type="hidden" value="' . $id . '" name="id">
                                                                    <button style="min-width:105px;" type="submit" name="restart" class="btn btn-sm btn-primary"><i class="bi bi-arrow-clockwise"></i> Reiniciar</button>
                                                                </form>';
                                                                }
                                                            }
                                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                                            $id = $registro['id'];
                                                            echo '<a href="editarmaquinado.php?id=' . $id . '" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                              <form action="codemaquinados.php" method="POST" class="d-inline">
                                  <button type="submit" name="delete" value="' . $id . '" class="btn btn-danger btn-sm m-1 deletebtn"><i class="bi bi-trash-fill"></i></button>
                              </form>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                    ?>
                        <div class="col-12 mt-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4>MAQUINADOS FINALIZADOS</h4>
                                </div>
                                <div class="card-body">
                                    <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
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
                                            $query = "SELECT proyecto.*, plano.*
                                        FROM plano 
                                        JOIN proyecto ON plano.idproyecto = proyecto.id
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

                                                            <!-- <form action="codemaquinados.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                            </form> -->
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
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tituloPlano">NUEVO PLANO</h1>
                    <h1 class="modal-title fs-5" id="tituloActividad" style="display: none;">NUEVA ACTIVIDAD</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codemaquinados.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-8 mb-3">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del proyecto
                                        $opcion = $proyecto['nombre'];
                                        // Obtener el ID del usuario
                                        $idProyecto = $proyecto['id'];
                                        // Mostrar la opción con el valor igual al ID del proyecto
                                        echo "<option value='$idProyecto' " . ($registro['id'] == $idProyecto ? 'selected' : '') . ">$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idproyecto">Proyecto asociado</label>
                        </div>

                        <div class="col-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="toggleElements()">
                                <label class="form-check-label" for="flexSwitchCheckDefault" id="labelPlano">Plano</label>
                                <label class="form-check-label" for="flexSwitchCheckDefault" id="labelActividad" style="display: none;">Actividad</label>
                            </div>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano" id="nombrePlano">Nombre del plano</label>
                            <label for="nombreplano" id="nombreActividad" style="display: none;">Nombre de la actividad</label>
                        </div>

                        <div class="mt-3" id="planoElements">
                            <label for="medio" class="form-label">Plano PDF</label>
                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
                        </div>

                        <div class="form-floating col-12 mt-3" id="actividadElements" style="display: none;">
                            <input type="text" class="form-control" name="actividad" id="actividad" placeholder="Actividad" autocomplete="off">
                            <label for="actividad">Detalles de la actividad</label>
                        </div>

                        <div class="form-floating col-12 col-md-5 mt-3">
                            <input type="text" class="form-control" name="piezas" id="piezas" placeholder="Piezas" autocomplete="off" required>
                            <label for="piezas">Número de piezas</label>
                        </div>

                        <div class="form-floating col-12 col-md-7 mt-3">
                            <select class="form-select" name="nivel" id="nivel" autocomplete="off" required>
                                <option selected disabled>Seleccione el nivel</option>
                                <option value="1">Nivel 1</option>
                                <option value="2">Nivel 2</option>
                                <option value="3">Nivel 3</option>
                                <option value="4">Nivel 4</option>
                            </select>
                            <label for="nivel">Nivel de pieza</label>
                        </div>

                        <div class="form-check col-12 mt-3 m-3">
                            <?php
                            // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                            $query = "SELECT * FROM usuarios WHERE rol = 8";
                            $result = mysqli_query($con, $query);

                            // Verificar si hay resultados
                            if (mysqli_num_rows($result) > 0) {
                                while ($usuario = mysqli_fetch_assoc($result)) {
                                    $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                    $idUsuario = $usuario['codigo'];

                                    // Cambio en el nombre del campo para que se envíen como un array
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

    <div class="modal fade" id="revision" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">HORA DE SALIDA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeasistencia.php" method="POST" class="row">
                        <input type="hidden" id="id" name="id" value="<?= $registro['id']; ?>">
                        <input type="hidden" id="codigo" name="codigo" value="<?= $registro['idcodigo']; ?>">
                        <div class="form-floating col-12 mb-3">
                            <input type="time" class="form-control" name="salida" id="salida" placeholder="Salida" autocomplete="off" required>
                            <label for="salida">Hora de salida</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="solicitar">Guardar</button>
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR MAQUINADO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codetecnicos.php" method="POST" class="row">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="idplano" id="idplano">
                                <option disabled selected>Seleccione un plano / actividad</option>
                                <?php
                                $query = "SELECT * FROM plano WHERE estatusplano = 1";
                                $result = mysqli_query($con, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    while ($plano = mysqli_fetch_assoc($result)) {
                                        $opcion = $plano['nombreplano'];
                                        $idPlano = $plano['id'];

                                        $queryAsignacion = "SELECT COUNT(*) as count FROM asignacionplano WHERE idplano = ?";
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
                            $query = "SELECT * FROM usuarios WHERE rol = 8";
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
                            <button type="submit" class="btn btn-primary" name="plano">Guardar</button>
                        </div>
                    </form>
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
    <script>document.getElementById('idplano').addEventListener('change', function() {
            console.log('Cambio detectado en idplano');
    var idPlano = this.value;

    // Hacer una solicitud AJAX al servidor para obtener los usuarios asignados
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'obtener_usuarios_asignados.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var usuariosAsignados = JSON.parse(xhr.responseText);
            console.log('Usuarios asignados:', usuariosAsignados); // Depuración

            // Habilitar todos los checkboxes primero
            var checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(function(checkbox) {
                checkbox.disabled = false;
            });

            // Deshabilitar los checkboxes de usuarios asignados
            usuariosAsignados.forEach(function(usuarioId) {
    var checkbox = document.getElementById('codigooperador_' + usuarioId);
    if (checkbox) {
        console.log('Deshabilitando checkbox:', checkbox);
        setTimeout(function() {
            checkbox.setAttribute('disabled', 'true');
            console.log('Estado del checkbox:', checkbox.disabled);
        }, 300);
    } else {
        console.log('Checkbox no encontrado:', 'codigooperador_' + usuarioId);
    }
});

        } else {
            console.error('Error en la solicitud AJAX:', xhr.statusText); // Depuración
        }
    };
    xhr.onerror = function() {
        console.error('Error en la solicitud AJAX'); // Depuración
    };
    xhr.send('idplano=' + encodeURIComponent(idPlano));
});

        function toggleElements() {
            var switchElement = document.getElementById('flexSwitchCheckDefault');
            var tituloPlano = document.getElementById('tituloPlano');
            var tituloActividad = document.getElementById('tituloActividad');
            var nombrePlano = document.getElementById('nombrePlano');
            var nombreActividad = document.getElementById('nombreActividad');
            var planoElements = document.getElementById('planoElements');
            var actividadElements = document.getElementById('actividadElements');

            if (switchElement.checked) {
                tituloPlano.style.display = 'none';
                tituloActividad.style.display = 'block';
                nombrePlano.style.display = 'none';
                nombreActividad.style.display = 'block';
                planoElements.style.display = 'none';
                actividadElements.style.display = 'block';
                labelPlano.style.display = 'none';
                labelActividad.style.display = 'block';
            } else {
                tituloPlano.style.display = 'block';
                tituloActividad.style.display = 'none';
                nombrePlano.style.display = 'block';
                nombreActividad.style.display = 'none';
                planoElements.style.display = 'block';
                actividadElements.style.display = 'none';
                labelPlano.style.display = 'block';
                labelActividad.style.display = 'none';
            }
        }

        $(document).ready(function() {
            $('#miTabla, #miTablaDos').DataTable({
                "order": [
                    [4, "asc"],
                    [5, "asc"]
                ] // Ordenar la primera columna (índice 0) en orden descendente
            });
        });

        // Función para cargar y mostrar el PDF en el iframe
        function showPDF(pdfUrl, iframeId) {
            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            loadingTask.promise.then(function(pdf) {
                // Carga la página 1 del PDF
                pdf.getPage(1).then(function(page) {
                    const scale = 1.5;
                    const viewport = page.getViewport({
                        scale
                    });

                    // Preparar el canvas para renderizar la página PDF
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    // Renderizar la página PDF en el canvas
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext).promise.then(function() {
                        // Agregar el canvas al iframe
                        const iframe = document.getElementById(iframeId);
                        iframe.src = canvas.toDataURL();
                    });
                });
            }, function(error) {
                console.error('Error al cargar el PDF:', error);
            });
        }

        const deleteButtons = document.querySelectorAll('.deletebtn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                const id = e.target.value; // Obtener el valor del botón delete

                // Mostrar la alerta de SweetAlert2 para confirmar la eliminación
                Swal.fire({
                    title: '¿Estás seguro que deseas eliminar este registro?',
                    text: '¡No podrás deshacer esta acción!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('delete', id);

                        fetch('codemaquinados.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);

                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });

        
    </script>
</body>

</html>