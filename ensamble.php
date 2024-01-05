<?php
session_start();
require 'dbcon.php';
header('Content-Type: text/html; charset=UTF-8');
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión
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
    $codigo = $_SESSION['codigo'];
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
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
    <title>Ensamble | Solara</title>
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
                                <h4>ENSAMBLES
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    T. Mecánico
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                    T. Control
                                </button>';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped text-center" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Proyecto</th>
                                            <th>Diagrama / actividad asociados</th>
                                            <th>Técnino asignado</th>
                                            <th>Número de piezas</th>
                                            <th>Prioridad</th>
                                            <th>Nivel de pieza</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [4, 8])) {
                                            $query = "SELECT proyecto.*, diagrama.*
                                                FROM diagrama 
                                                JOIN proyecto ON diagrama.idproyecto = proyecto.id 
                                                JOIN asignaciondiagrama ON asignaciondiagrama.idplano = diagrama.id 
                                                JOIN usuarios ON asignaciondiagrama.codigooperador = usuarios.codigo
                                                WHERE asignaciondiagrama.codigooperador = $codigo 
                                                AND (diagrama.estatusplano = 1 OR diagrama.estatusplano = 2)
                                                ORDER BY proyecto.prioridad ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                            $query = "SELECT proyecto.*, diagrama.*
                                                FROM diagrama 
                                                JOIN proyecto ON diagrama.idproyecto = proyecto.id
                                                WHERE (diagrama.estatusplano = 1 OR diagrama.estatusplano = 2)
                                                ORDER BY proyecto.prioridad asc";
                                        }
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [4,8])) {
                                                            if (empty($registro['medio'])) {
                                                                ?>
                                                                <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?></p>
                                                            <?php
                                                            } else {
                                                                ?>
                                                                <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Diagrama <?= $registro['nombreplano']; ?></button>
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
                                                        ?>
                                                            <?php
                                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                                            if (empty($registro['medio'])) {
                                                                ?>
                                                                <p><b><?= $registro['nombreplano']; ?>:</b> <?= $registro['actividad']; ?></p>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <a href="verdiagrama.php?id=<?= $registro['id']; ?>" class="btn btn-outline-dark btn-sm">Diagrama <?= $registro['nombreplano']; ?></a>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </td>
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
                                                            echo 'No asignado';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $registro['piezas']; ?></td>
                                                    <td><?= $registro['prioridad']; ?></td>
                                                    <?php
                                                        if ($registro['nivel'] === '1') {
                                                            echo "<td style='background-color:#e50000 !important'>Nivel 1</td>";
                                                        } elseif ($registro['nivel'] === '2') {
                                                            echo "<td style='background-color:#e56f00 !important'>Nivel 2</td>";
                                                        } elseif ($registro['nivel'] === '3') {
                                                            echo "<td style='background-color:#e5da00 !important'>Nivel 3</td>";
                                                        } elseif ($registro['nivel'] === '4') {
                                                            echo "<td style='background-color:#17e500 !important'>Nivel 4</td>";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    <td>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [4, 8])) {
                                                            $id = $registro['id'];
                                                            if ($registro['estatusplano'] === '1') {
                                                                echo '<a href="inicioactividadesensamble.php?id=' . $id . '" class="btn btn-success btn-sm m-1">Iniciar</a>';
                                                            } else if ($registro['estatusplano'] === '2') {
                                                                echo '<form action="codeactividad.php" method="post">
                                                                <input type="hidden" value="' . $id . '" name="id">
                                                                <button type="submit" name="restartensamble" class="btn btn-sm btn-primary">Seguimiento</button>
                                                                </form>';
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                                            $id = $registro['id'];
                                                            echo '<a href="editardiagrama.php?id=' . $id . '" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <form action="codediagramas.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="' . $id . '" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                    ?>
                        <div class="col-12 mt-3">
                            <div class="card">
                            <div class="card-header"><h4>ENSAMBLES FINALIZADOS</h4></div>
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
                                                                <a href="verplano.php?id=<?= $registro['id']; ?>" class="btn btn-outline-dark btn-sm">Diagrama <?= $registro['nombreplano']; ?></a>
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
                                                <td><?= $registro['prioridad']; ?></td>
                                                <?php
                                                        if ($registro['nivel'] === '1') {
                                                            echo "<td style='background-color:#e50000 !important'>Nivel 1</td>";
                                                        } elseif ($registro['nivel'] === '2') {
                                                            echo "<td style='background-color:#e56f00 !important'>Nivel 2</td>";
                                                        } elseif ($registro['nivel'] === '3') {
                                                            echo "<td style='background-color:#e5da00 !important'>Nivel 3</td>";
                                                        } elseif ($registro['nivel'] === '4') {
                                                            echo "<td style='background-color:#17e500 !important'>Nivel 4</td>";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                <td>
                                                    <a href="editardiagrama.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                    <!-- <form action="codediagramas.php" method="POST" class="d-inline">
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
    <!-- Modal Mecanica-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tituloPlano">NUEVO DIAGRAMA</h1>
                    <h1 class="modal-title fs-5" id="tituloActividad" style="display: none;">NUEVA ACTIVIDAD</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codediagramas.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-8 mb-3">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['nombre'];
                                        $idProyecto = $proyecto['id'];
                                        echo "<option value='$idProyecto'>$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idproyecto">Proyecto asociado</label>
                        </div>

                        <div class="col-4 mb-3">
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="toggleElements()">
                                <label class="form-check-label" for="flexSwitchCheckDefault" id="labelPlano">Diagrama</label>
                                <label class="form-check-label" for="flexSwitchCheckDefault" id="labelActividad" style="display: none;">Actividad</label>
                            </div>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano" id="nombrePlano">Nombre del diagrama</label>
                            <label for="nombreplano" id="nombreActividad" style="display: none;">Nombre de la actividad</label>
                        </div>

                        <div class="mt-3" id="planoElements">
                            <label for="medio" class="form-label">Diagrama PDF</label>
                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
                        </div>

                        <div class="form-floating col-12 mt-3" id="actividadElements" style="display: none;">
                            <select class="form-select" name="actividad" id="actividad">
                                <option disabled selected>Seleccione una actividad</option>
                                <?php
                                $query = "SELECT * FROM actividadesmecanica";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['actividad'];
                                        echo "<option value='$opcion'>$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="actividad">Actividad</label>
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
                            <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Control-->
     <div class="modal fade" id="exampleModalDos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tituloPlanoDos">NUEVO DIAGRAMA T. CONTROL</h1>
                    <h1 class="modal-title fs-5" id="tituloActividadDos" style="display: none;">NUEVA ACTIVIDAD T. CONTROL</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codediagramas.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-8 mb-3">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['nombre'];
                                        $idProyecto = $proyecto['id'];
                                        echo "<option value='$idProyecto'>$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idproyecto">Proyecto asociado</label>
                        </div>

                        <div class="col-4 mb-3">
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefaultDos" onchange="toggleElementsDos()">
                                <label class="form-check-label" for="flexSwitchCheckDefaultDos" id="labelPlanoDos">Diagrama</label>
                                <label class="form-check-label" for="flexSwitchCheckDefaultDos" id="labelActividadDos" style="display: none;">Actividad</label>
                            </div>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano" id="nombrePlanoDos">Nombre del diagrama</label>
                            <label for="nombreplano" id="nombreActividadDos" style="display: none;">Nombre de la actividad</label>
                        </div>

                        <div class="mt-3" id="planoElementsDos">
                            <label for="medio" class="form-label">Diagrama PDF</label>
                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
                        </div>

                        <div class="form-floating col-12 mt-3" id="actividadElementsDos" style="display: none;">
                            <select class="form-select" name="actividad" id="actividad">
                                <option disabled selected>Seleccione una actividad</option>
                                <?php
                                $query = "SELECT * FROM actividadescontrol";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['actividad'];
                                        echo "<option value='$opcion'>$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="actividad">Actividad</label>
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
                            $query = "SELECT * FROM usuarios WHERE rol = 4";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <!-- Incluir los archivos de PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
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
    function toggleElementsDos() {
        var switchElement = document.getElementById('flexSwitchCheckDefaultDos');
        var tituloPlanoDos = document.getElementById('tituloPlanoDos');
        var tituloActividadDos = document.getElementById('tituloActividadDos');
        var nombrePlanoDos = document.getElementById('nombrePlanoDos');
        var nombreActividadDos = document.getElementById('nombreActividadDos');
        var planoElementsDos = document.getElementById('planoElementsDos');
        var actividadElementsDos = document.getElementById('actividadElementsDos');
        if (switchElement.checked) {
            tituloPlanoDos.style.display = 'none';
            tituloActividadDos.style.display = 'block';
            nombrePlanoDos.style.display = 'none';
            nombreActividadDos.style.display = 'block';
            planoElementsDos.style.display = 'none';
            actividadElementsDos.style.display = 'block';
            labelPlanoDos.style.display = 'none';
            labelActividadDos.style.display = 'block';
        } else {
            tituloPlanoDos.style.display = 'block';
            tituloActividadDos.style.display = 'none';
            nombrePlanoDos.style.display = 'block';
            nombreActividadDos.style.display = 'none';
            planoElementsDos.style.display = 'block';
            actividadElementsDos.style.display = 'none';
            labelPlanoDos.style.display = 'block';
            labelActividadDos.style.display = 'none';
        }
    }

        $(document).ready(function() {
            $('#miTabla, #miTablaDos').DataTable({
                "order": [
                    [4, "asc"]
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
    </script>
</body>
</html>