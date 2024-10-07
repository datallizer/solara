<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
header('Content-Type: text/html; charset=UTF-8');
$codigo = $_SESSION['codigo'];
$query_check = "SELECT 1 FROM plano 
                INNER JOIN asignacionplano ON plano.id = asignacionplano.idplano 
                WHERE asignacionplano.codigooperador = '$codigo' AND plano.estatusplano = 3 
                LIMIT 1";

$result_check = mysqli_query($con, $query_check);
$messageWarning = '';
if (mysqli_num_rows($result_check) > 0) {
    $messageWarning = "No detuviste correctamente un maquinado en tu última sesión, SOLARA AI registrara esta llamada de atención";
}

// Genera el mensaje de sesión si aplica
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';

// Elimina el mensaje de sesión después de usarlo
unset($_SESSION['message']);

// Comienza el script de JavaScript
echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            function showAlert(title, text, icon, callback) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            }

            // Muestra la primera alerta si existe el mensaje de advertencia
            " . (!empty($messageWarning) ? "showAlert('ADVERTENCIA', " . json_encode($messageWarning) . ", 'warning', function() {" : "") . "
            
            // Muestra la segunda alerta si existe el mensaje de sesión
            " . (!empty($message) ? "showAlert('NOTIFICACIÓN', " . json_encode($message) . ", 'info');" : "") . "
            
            " . (!empty($messageWarning) ? "});" : "") . "
        });
      </script>";

if (isset($_SESSION['codigo'])) {
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
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
    <title>Maquinados | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>
<style>
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
    }

    .spinner-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .spinner {
        width: 3rem;
        height: 3rem;
    }
</style>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <?php include 'mensajes.php'; ?>
    <?php include 'modales.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>MAQUINADOS ACTIVOS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Nuevo plano
                                </button> 
                                <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalAsignar">
                                    Asignar operador
                                </button>
                                <a href="maquinadosfinalizados.php" class="btn btn-primary btn-sm" id="floatingButton">
                                Maquinados<br>finalizados
                            </a>';
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
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                                echo '<th>Operadores asignados</th>';
                                            }
                                            ?>
                                            <th>Número de piezas</th>
                                            <th>Prioridad</th>
                                            <th>Nivel de pieza</th>
                                            <th>Estatus</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8, 13])) {
                                            $query = "SELECT proyecto.id, proyecto.prioridad, proyecto.nombre, plano.*
                                            FROM plano 
                                            JOIN proyecto ON plano.idproyecto = proyecto.id 
                                            JOIN asignacionplano ON asignacionplano.idplano = plano.id 
                                            JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                            WHERE asignacionplano.codigooperador = $codigo 
                                            AND (plano.estatusplano = 1 OR plano.estatusplano = 2 OR plano.estatusplano = 3) ORDER BY proyecto.prioridad ASC, plano.nivel ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                            $query = "SELECT proyecto.id, proyecto.prioridad, proyecto.nombre, plano.id, plano.idproyecto, plano.nivel, plano.piezas, plano.estatusplano, plano.actividad, plano.nombreplano, plano.medio
                                            FROM plano 
                                            JOIN proyecto ON plano.idproyecto = proyecto.id
                                            WHERE (plano.estatusplano = 1 OR plano.estatusplano = 2 OR plano.estatusplano = 3) ORDER BY proyecto.prioridad asc";
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
                                                        // Verifica si 'medio' está vacío o no
                                                        if (empty($registro['medio'])) {
                                                        ?>
                                                            <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?> </p>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <button style="min-width: 200px;" type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>"><i class="bi bi-file-pdf"></i> Plano <?= $registro['nombreplano']; ?></button>
                                                            <div style="max-height: 95vh;" class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="pdfModalLabel"><?= $registro['nombreplano']; ?></h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <iframe src="<?= $registro['medio']; ?>" width="100%" height="400px"></iframe>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <a href="imprimirplano.php?id=<?= $registro['id']; ?>" class="btn btn-primary"><i class="bi bi-file-pdf"></i> Imprmir</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                                    ?>
                                                        <td>
                                                            <?php
                                                            $queryAsignacion = "SELECT asignacionplano.*, asignacionplano.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                        FROM asignacionplano
                                                        JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                                        WHERE asignacionplano.idplano = " . $registro['id'];
                                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                                foreach ($query_run_asignacion as $asignacion) {
                                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {

                                                                        if ($registro['estatusplano'] === '1') {
                                                            ?>
                                                                            <form class="deleteForm" action="codencargados.php" method="post">
                                                                                <div style="display: flex; align-items: center;">
                                                                                    <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                                                    <button type="button" class="deleteButton" name="deleteplano" style="border: none;" class="btn btn-sm" data-id="<?= $asignacion['id_encargado']; ?>">
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
                                                                    } else if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8, 13])) {
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
                                                        $motivosQuery = "SELECT motivo FROM motivosinicio";
                                                        $motivosResult = mysqli_query($con, $motivosQuery);
                                                        $motivosOptions = "";
                                                        while ($row = mysqli_fetch_assoc($motivosResult)) {
                                                            $motivosOptions .= '<option value="' . $row['motivo'] . '">' . $row['motivo'] . '</option>';
                                                        }
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8, 13])) {
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
                                                                        $botonTexto = '<i class="bi bi-play-fill"></i> Iniciar';
                                                                        $botonClase = 'btn-outline-success';
                                                                        $botonEstado = 'disabled';
                                                                    }

                                                                    if ($botonEstado === 'disabled') {
                                                                        echo '<button id="btn-' . $id . '" style="min-width: 105px;" class="btn btn-sm ' . $botonClase . '" onclick="handleNonPriorityClick(\'' . $id . '\')">' . $botonTexto . '</button>';
                                                                    } else {
                                                                        echo '<form action="codeactividad.php" method="post">
                        <input type="hidden" value="' . $registro['id'] . '" name="id">
                        <button style="min-width: 105px;" type="submit" name="start" class="btn btn-sm ' . $botonClase . '">' . $botonTexto . '</button>
                      </form>';
                                                                    }
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
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tituloPlano">NUEVO PLANO</h1>
                    <h1 class="modal-title fs-5" id="tituloActividad" style="display: none;">NUEVA ACTIVIDAD</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codemaquinados.php" method="POST" class="row mb-0" enctype="multipart/form-data">
                        <div class="col-7 mb-3">
                            <div class="row">
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

                                <div class="form-floating mt-1">
                                    <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                                    <label for="nombreplano" id="nombrePlano">Nombre del plano</label>
                                    <label for="nombreplano" id="nombreActividad" style="display: none;">Nombre de la actividad</label>
                                </div>

                                <div class="mt-3" id="planoElements">
                                    <label for="medio" class="form-label">Plano PDF</label>
                                    <input class="form-control" type="file" id="medio" name="medio" max="100000">
                                </div>

                                <div class="form-floating mt-3" id="actividadElements" style="display: none;">
                                    <input type="text" class="form-control" name="actividad" id="actividad" placeholder="Actividad" autocomplete="off">
                                    <label for="actividad">Detalles de la actividad</label>
                                </div>

                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control" name="piezas" id="piezas" placeholder="Piezas" autocomplete="off" required>
                                    <label for="piezas">Número de piezas</label>
                                </div>

                                <div class="form-floating mt-3">
                                    <select class="form-select" name="nivel" id="nivel" autocomplete="off" required>
                                        <option selected disabled>Seleccione el nivel</option>
                                        <option value="1">Nivel 1</option>
                                        <option value="2">Nivel 2</option>
                                        <option value="3">Nivel 3</option>
                                        <option value="4">Nivel 4</option>
                                    </select>
                                    <label for="nivel">Nivel de pieza</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-5">
                            <div class="form-check mt-3 m-3">
                                <?php
                                // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                                $query = "SELECT * FROM usuarios WHERE (rol = 8 OR rol = 13) AND estatus = 1";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($usuario = mysqli_fetch_assoc($result)) {
                                        $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                        $idUsuario = $usuario['codigo'];
                                        $idMedio = $usuario['medio'];

                                        // Cambio en el nombre del campo para que se envíen como un array
                                        echo "<input class='form-check-input mb-2' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                        echo "<label class='form-check-label mb-2' for='codigooperador_$idUsuario'><img style='width:40px;' src='$idMedio' alt=''> $nombreCompleto</label><br>";
                                    }
                                }
                                ?>
                            </div>
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
                            $query = "SELECT * FROM usuarios WHERE rol = 8 OR rol = 13 AND estatus = 1";
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

    <div class="spinner-overlay" style="z-index: 9999;">
        <div class="spinner-container">
            <div class="spinner-grow text-primary spinner" role="status">
                <span class="visually-hidden">Loading...</span>
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
        document.getElementById('idplano').addEventListener('change', function() {
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

            // Cambiar a usar clase en lugar de ID
            $('.deleteButton').on('click', function(event) {
                event.preventDefault(); // Previene el envío del formulario por defecto

                const form = $(this).closest('form'); // Encuentra el formulario más cercano al botón
                const deleteValue = $(this).data('id'); // Obtiene el valor del data-id del botón

                Swal.fire({
                    title: 'ADVERTENCIA',
                    text: '¿Estás seguro que deseas eliminar la asignación del maquinado al usuario actual? Deberás asignar un usuario nuevo.',
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
                            name: 'deleteplano',
                            value: deleteValue
                        }).appendTo(form);

                        // Si el usuario confirma, se envía el formulario
                        form.submit();
                    }
                });
            });


            $('form').on('submit', function(e) {
                e.preventDefault(); // Evitar el envío inmediato del formulario

                // Mostrar el overlay con el spinner
                $('.spinner-overlay').show();

                // Obtener el valor del botón que se hizo clic
                var buttonName = $(this).find('button[type=submit]:focus').attr('name');

                // Simular un retraso para demostrar el spinner (elimina esto en producción)
                var form = this;
                // Agregar el valor del botón como un campo oculto al formulario
                $('<input>').attr({
                    name: buttonName
                }).appendTo(form);

                // Enviar el formulario después del retraso simulado
                form.submit();

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

        function handleNonPriorityClick(id) {
            Swal.fire({
                title: 'ACTIVIDAD NO PRIORITARIA',
                html: `
            <p>Selecciona un motivo para poder iniciar esta actividad que no es prioritaria</p>
            <select id="motivoSelect" class="swal2-select">
                <option value="">Seleccione un motivo</option>
                <?php echo $motivosOptions; ?>
            </select>
        `,
                showCancelButton: true,
                confirmButtonText: 'Iniciar actividad',
                preConfirm: () => {
                    const motivo = document.getElementById('motivoSelect').value;
                    if (!motivo) {
                        Swal.showValidationMessage('Debes seleccionar un motivo');
                        return false;
                    }
                    return {
                        motivo: motivo
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'codeactividad.php';

                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'id';
                    inputId.value = id;
                    form.appendChild(inputId);

                    const inputMotivo = document.createElement('input');
                    inputMotivo.type = 'hidden';
                    inputMotivo.name = 'motivo';
                    inputMotivo.value = result.value.motivo;
                    form.appendChild(inputMotivo);

                    const inputAction = document.createElement('input');
                    inputAction.type = 'hidden';
                    inputAction.name = 'start';
                    inputAction.value = 'start ';
                    form.appendChild(inputAction);

                    document.body.appendChild(form);
                    form.submit();
                }
            });

        }
    </script>
</body>

</html>