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
    <title>Anteproyectos | Solara</title>
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
                                <h4>ANTEPROYECTOS ACTIVOS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo anteproyecto
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                        Asignar encargado
                                        </button>';
                                    }
                                    ?>
                                </h4>
                                <a href="anteproyectosfinalizados.php" class="btn btn-primary btn-sm" id="floatingButton">
                                    Anteproyectos<br>finalizados
                                </a>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Cliente</th>
                                            <th>Etapa</th>
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                echo '<th>Encargado de anteproyecto</th>';
                                            }
                                            ?>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM proyecto WHERE estatus = 2 ORDER BY prioridad ASC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {

                                                $proyecto_id = $registro['id'];
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p class="text-center"><?= $proyecto_id ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-center"><?= $registro['nombre']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-center"><?= $registro['cliente']; ?></p>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        if ($registro['etapa'] === '2') {
                                                        ?>
                                                            <p style="background-color: #ffeacc;padding:5px;border-radius:5px;">Etapa 2 de 5</p>
                                                            <p>Visita levantamiento con cliente</p>
                                                            <?php
                                                            if (in_array($_SESSION['rol'], [1, 2, 5])) {
                                                                $query = "SELECT * FROM agendaproyectos WHERE estatus = 1 AND idproyecto = '" . $proyecto_id . "'";
                                                                $query_run = mysqli_query($con, $query);
                                                                if (mysqli_num_rows($query_run) > 0) {
                                                                    foreach ($query_run as $registro) {
                                                            ?>
                                                                        <p><i class="bi bi-calendar-check"></i> <?= $registro['dia']; ?> <i class="bi bi-clock"></i> <?= $registro['hora']; ?></p>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        <?php
                                                        } else if ($registro['etapa'] === '3') {
                                                        ?>
                                                            <p style="background-color: #ffeacc;padding:5px;border-radius:5px;">Etapa 3 de 5</p>
                                                            <p>Generacion de diseño / diagrama a bloques</p>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <?php
                                                                    $query = "SELECT * FROM proyectomedios WHERE (estatus = 1 OR estatus = 2 OR estatus = 3)  AND idproyecto = '" . $proyecto_id . "'";
                                                                    $query_run = mysqli_query($con, $query);
                                                                    if (mysqli_num_rows($query_run) > 0) {
                                                                        foreach ($query_run as $medio) {
                                                                            $idmodal = $medio['id'];
                                                                    ?>
                                                                            <?php
                                                                            if ($medio['estatus'] == 1) {
                                                                            ?>
                                                                                <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $medio['id']; ?>"><?= $medio['tipo']; ?> a bloques</button>
                                                                            <?php
                                                                            } elseif ($medio['estatus'] == 2) {
                                                                            ?>
                                                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $medio['id']; ?>"><?= $medio['tipo']; ?> a bloques rechazado</button>
                                                                            <?php
                                                                            } elseif ($medio['estatus'] == 3) {
                                                                            ?>
                                                                                <button class="btn btn-success btn-sm" disabled><?= $medio['tipo']; ?> aprobado</button>
                                                                            <?php
                                                                            }
                                                                            ?>

                                                                            <div class="modal fade" id="pdfModal<?= $medio['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                                                <div class="modal-dialog modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 style="text-transform: uppercase;" class="modal-title" id="pdfModalLabel"><?= $medio['tipo']; ?> a bloques pdfModal<?= $medio['id']; ?></h5>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <iframe src="<?= $medio['medio']; ?>" width="100%" height="400px"></iframe>
                                                                                        </div>
                                                                                        <?php
                                                                                        if (in_array($_SESSION['rol'], [1, 2])) {
                                                                                            if ($medio['estatus'] == 1) {
                                                                                        ?>
                                                                                                <div class="modal-footer mb-3">
                                                                                                    <form action="codeproyecto.php" method="post" id="formularioBloque">
                                                                                                        <input type="hidden" name="id" value="<?= $medio['id']; ?>">
                                                                                                        <input type="hidden" name="idproyecto" value="<?= $medio['idproyecto']; ?>">
                                                                                                        <!-- Se rechaza con estatus 2 -->
                                                                                                        <button type="button" class="btn btn-danger rechazarbloque" data-modal-id="pdfModal<?= $medio['id']; ?>">Rechazar</button>

                                                                                                        <!-- Se aprueba con estatus 3 -->
                                                                                                        <button type="submit" class="btn btn-success" name="aprobarbloque">Aprobar <?= $medio['tipo']; ?></button>
                                                                                                    </form>
                                                                                                </div>
                                                                                        <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        } else if ($registro['etapa'] === '4') {
                                                        ?>
                                                            <p style="background-color: #ffeacc;padding:5px;border-radius:5px;">Etapa 4 de 5</p>
                                                            <p>Generación de BOM's</p>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <?php
                                                                    $query = "SELECT * FROM proyectoboms WHERE (estatus = 1 OR estatus = 2 OR estatus = 3)  AND idproyecto = '" . $proyecto_id . "'";
                                                                    $query_run = mysqli_query($con, $query);
                                                                    if (mysqli_num_rows($query_run) > 0) {
                                                                        foreach ($query_run as $registroBom) {
                                                                            $idmodal = $registroBom['id'];
                                                                    ?>
                                                                            <?php
                                                                            if ($registroBom['estatus'] == 1) {
                                                                            ?>
                                                                                <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registroBom['id']; ?>">BOM <?= $registroBom['tipo']; ?></button>
                                                                            <?php
                                                                            } elseif ($registroBom['estatus'] == 2) {
                                                                            ?>
                                                                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registroBom['id']; ?>">BOM <?= $registroBom['tipo']; ?> rechazado</button>
                                                                            <?php
                                                                            } elseif ($registroBom['estatus'] == 3) {
                                                                            ?>
                                                                                <button class="btn btn-success btn-sm" disabled>BOM <?= $registroBom['tipo']; ?> aprobado</button>
                                                                            <?php
                                                                            }
                                                                            ?>

                                                                            <div class="modal fade" id="pdfModal<?= $registroBom['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                                                <div class="modal-dialog modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 style="text-transform: uppercase;" class="modal-title" id="pdfModalLabel"><?= $registroBom['tipo']; ?> a bloques pdfModal<?= $registroBom['id']; ?></h5>
                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body text-center">
                                                                                        <a href="<?= $registroBom['medio'] ?>" download="<?= $registroBom['medio'] ?>" class="btn btn-primary"><i style="font-size: 100px;" class="bi bi-filetype-xlsx"></i><p>Descargar Excel</p></a>
                                                                                        
                                                                                        <p class="mt-3">Despues de visualizar el excel deberás regresar para aprobarlo o rechazarlo manualmete</p>
                                                                                        </div>
                                                                                        <?php
                                                                                        if (in_array($_SESSION['rol'], [1, 2])) {
                                                                                            if ($registroBom['estatus'] == 1) {
                                                                                        ?>
                                                                                                <div class="modal-footer mb-3">
                                                                                                    <form action="codeproyecto.php" method="post" id="formularioBloque">
                                                                                                        <input type="hidden" name="id" value="<?= $registroBom['id']; ?>">
                                                                                                        <input type="hidden" name="idproyecto" value="<?= $registroBom['idproyecto']; ?>">
                                                                                                        <!-- Se rechaza con estatus 2 -->
                                                                                                        <button type="button" class="btn btn-danger rechazarBom" data-modal-id="pdfModal<?= $registroBom['id']; ?>">Rechazar</button>
                                                                                                        <!-- Se aprueba con estatus 3 -->
                                                                                                        <button type="submit" class="btn btn-success" name="aprobarBom">Aprobar <?= $registroBom['tipo']; ?></button>
                                                                                                    </form>
                                                                                                </div>
                                                                                        <?php
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        } else if ($registro['etapa'] === '5') {
                                                        ?>
                                                            <p style="background-color: #ffeacc;padding:5px;border-radius:5px;">Etapa 5 de 5</p>
                                                            <p>Cotización</p>
                                                            <?php
                                                            $query = "SELECT * FROM proyectoboms WHERE estatus = 3 AND idproyecto = '" . $proyecto_id . "'";
                                                            $query_run = mysqli_query($con, $query);
                                                            if (mysqli_num_rows($query_run) > 0) {
                                                                foreach ($query_run as $registroMonto) {
                                                                    $idmodal = $registroMonto['id'];
                                                            ?>
                                                                    <p disabled><b>BOM <?= $registroMonto['tipo']; ?>:</b> $<?= $registroMonto['monto']; ?></p>
                                                                <?php
                                                                }
                                                                ?>


                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </td>

                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                    ?>
                                                        <td>
                                                            <?php
                                                            $queryAsignacion = "SELECT encargadoproyecto.*, encargadoproyecto.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                        FROM encargadoproyecto
                                                        JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo 
                                                        WHERE encargadoproyecto.idproyecto = " . $proyecto_id;
                                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                                foreach ($query_run_asignacion as $asignacion) {
                                                            ?>

                                                                    <form class="deleteForm" action="codencargados.php" method="post">
                                                                        <div style="display: flex; align-items: center;">
                                                                            <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
                                                                            <input type="hidden" name="antelocation" value="1">
                                                                            <button type="button" class="deleteButton" name="deleteproyecto" style="border: none;" class="btn btn-sm" data-id="<?= $asignacion['id']; ?>">
                                                                                <i style="color: #d41111;" class="bi bi-x-lg"></i>
                                                                            </button>
                                                                        </div>
                                                                    </form>


                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <td>
                                                        <a style="color:#fff;" href="editarproyecto.php?id=<?= $proyecto_id; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                            if ($registro['etapa'] == 5) {
                                                                echo '<form action="codeproyecto.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="id" value="' . $proyecto_id . '">
                                                                        <button type="submit" name="aprobar" class="btn btn-success btn-sm m-1"><i class="bi bi-check2-circle"></i></button>
                                                                    </form>';
                                                            }
                                                            echo '<form action="codeproyecto.php" method="POST" class="d-inline">
                                                                <input type="hidden" name="id" value="' . $proyecto_id . '">
                                                                        <button type="submit" name="archivaranteproyecto" class="btn btn-danger btn-sm m-1"><i class="bi bi-x-circle"></i></button>
                                                                    </form>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='6'><p>No se encontro ningun registro</p></td>";
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO ANTEPROYECTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codeproyecto.php" method="POST" class="row mb-0">
                        <div class="col-7 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                                <label style="margin-left: 0px !important;" for="nombre">Nombre del proyecto</label>
                            </div>

                            <div class="form-floating mt-3">
                                <input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nombre" autocomplete="off" required>
                                <label style="margin-left: 0px !important;" for="cliente">Cliente</label>
                            </div>

                            <div class="form-floating mt-3 mt-3">
                                <select class="form-select" name="etapa" id="etapa" autocomplete="off" required>
                                    <option disabled>Seleccione una etapa</option>
                                    <option selected value="2">Recepción de RFQ</option>
                                    <option value="2">Visita/levantamiento con cliente</option>
                                    <option value="3">Generación de diseño/diagrama a bloques</option>
                                    <option value="4">Generación de BOM's</option>
                                    <option value="5">Cotización</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapa">Etapa de pretrabajo:</label>
                            </div>

                        </div>

                        <div class="col-5">
                            <div class="form-check mt-3 m-3">
                                <?php
                                // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                                $query = "SELECT * FROM usuarios WHERE rol IN (5,9,13) AND estatus = 1";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($usuario = mysqli_fetch_assoc($result)) {
                                        $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                        if ($usuario['rol'] == 5) {
                                            $idRol = 'Ing. Diseño';
                                        } elseif ($usuario['rol'] == 9) {
                                            $idRol = 'Ing. Control';
                                        } elseif ($usuario['rol'] == 13) {
                                            $idRol = 'Ing. Laser';
                                        }
                                        $idUsuario = $usuario['codigo'];
                                        $idMedio = $usuario['medio'];

                                        // Cambio en el nombre del campo para que se envíen como un array
                                        echo "<input class='form-check-input mb-2' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                        echo "<label class='form-check-label mb-2' for='codigooperador_$idUsuario'><img style='width:40px;' src='$idMedio' alt=''> $nombreCompleto - $idRol</label><br>";
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="anteproyecto">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="exampleModalDos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ENCARGADO DE PROYECTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codetecnicos.php" method="POST" class="row mb-0">
                        <div class="form-floating col-12">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM proyecto WHERE estatus = 2";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del proyecto
                                        $opcion = $proyecto['nombre'];
                                        // Obtener el ID del usuario
                                        $idProyecto = $proyecto['id'];
                                        // Mostrar la opción con el valor igual al ID del proyecto
                                        echo "<option value='$idProyecto' " . ($proyecto_id == $idProyecto ?: '') . ">$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label style="margin-left: 10px;" for="idproyecto">Proyecto a asignar</label>
                        </div>

                        <div class="form-check col-12 mt-3">
                            <?php
                            // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                            $query = "SELECT * FROM usuarios WHERE rol IN (5,9,13) AND estatus = 1";
                            $result = mysqli_query($con, $query);

                            // Verificar si hay resultados
                            if (mysqli_num_rows($result) > 0) {
                                while ($usuario = mysqli_fetch_assoc($result)) {
                                    $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                    $idUsuario = $usuario['codigo'];
                                    $idMedio = $usuario['medio'];
                                    if ($usuario['rol'] == 5) {
                                        $idRol = 'Ing. Diseño';
                                    } elseif ($usuario['rol'] == 9) {
                                        $idRol = 'Ing. Control';
                                    } elseif ($usuario['rol'] == 13) {
                                        $idRol = 'Ing. Laser';
                                    }
                                    // Cambio en el nombre del campo para que se envíen como un array
                                    echo "<input  style='margin-right: 10px;' class='form-check-inputmb-2' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                    echo "<label class='form-check-label mb-2' for='codigooperador_$idUsuario'><img style='width:40px;' src='$idMedio' alt=''> $nombreCompleto - $idRol</label><br>";
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" name="antelocation" value="1">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="proyecto">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="spinner-overlay" style="z-index: 99999999999999999999999;">
        <div class="spinner-container">
            <div class="spinner-grow text-primary spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        $(document).ready(function() {
            // Cambiar a usar clase en lugar de ID
            $('.deleteButton').on('click', function(event) {
                event.preventDefault(); // Previene el envío del formulario por defecto
                const form = $(this).closest('form'); // Encuentra el formulario más cercano al botón
                const deleteValue = $(this).data('id'); // Obtiene el valor del data-id del botón
                Swal.fire({
                    title: 'ADVERTENCIA',
                    text: '¿Estás seguro que deseas eliminar la asignación del proyecto al usuario actual? Deberás asignar un usuario nuevo.',
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
                            name: 'deleteproyecto',
                            value: deleteValue
                        }).appendTo(form);
                        // Si el usuario confirma, se envía el formulario
                        form.submit();
                    }
                });
            });
            $('#miTabla').DataTable({
                "order": [
                    [6, "asc"]
                ] // Ordenar la primera columna (índice 0) en orden descendente
            });

            $('form').on('submit', function(e) {
                e.preventDefault();

                $('.spinner-overlay').show(); // Muestra el spinner

                var buttonName = $(this).find('button[type=submit]:focus').attr('name');

                var form = this;
                $('<input>').attr({
                    type: 'hidden',
                    name: buttonName
                }).appendTo(form);

                form.submit();
            });
        });

        document.getElementById('miFormulario').addEventListener('submit', function(event) {
            const checkboxes = document.querySelectorAll('input[name="codigooperador[]"]');
            let alMenosUnoMarcado = false;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    alMenosUnoMarcado = true;
                }
            });
            if (!alMenosUnoMarcado) {
                alert('Por favor, seleccione al menos un usuario encargado.');
                event.preventDefault();
            }
        });

        document.querySelectorAll('.rechazarbloque').forEach(button => {
            button.addEventListener('click', function() {
                let modalId = this.getAttribute('data-modal-id');
                let modalElement = document.getElementById(modalId);
                if (modalElement) {
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }

                Swal.fire({
                    title: 'Detalles de corrección',
                    input: 'textarea',
                    inputPlaceholder: 'Escribe los detalles para la corrección...',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) {
                            return '¡Por favor ingresa los detalles!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = this.closest('form'); // Encuentra el formulario más cercano al botón

                        let rechazarInput = document.createElement('input');
                        rechazarInput.type = 'hidden';
                        rechazarInput.name = 'rechazarbloque';
                        rechazarInput.value = '1';
                        form.appendChild(rechazarInput);

                        let detallesInput = document.createElement('input');
                        detallesInput.type = 'hidden';
                        detallesInput.name = 'detalles';
                        detallesInput.value = result.value;
                        form.appendChild(detallesInput);

                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.rechazarBom').forEach(button => {
            button.addEventListener('click', function() {
                let modalId = this.getAttribute('data-modal-id'); // Obtener ID del modal desde data-attribute
                let modalElement = document.getElementById(modalId);

                if (modalElement) {
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }


                Swal.fire({
                    title: 'Detalles de corrección',
                    input: 'textarea',
                    inputPlaceholder: 'Escribe los detalles para la corrección...',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) {
                            return '¡Por favor ingresa los detalles!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = this.closest('form'); // Encuentra el formulario más cercano al botón

                        let rechazarInput = document.createElement('input');
                        rechazarInput.type = 'hidden';
                        rechazarInput.name = 'rechazarBom';
                        rechazarInput.value = '1';
                        form.appendChild(rechazarInput);

                        let detallesInput = document.createElement('input');
                        detallesInput.type = 'hidden';
                        detallesInput.name = 'detalles';
                        detallesInput.value = result.value;
                        form.appendChild(detallesInput);

                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>