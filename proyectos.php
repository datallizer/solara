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
    <?php include 'modales.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>PROYECTOS ACTIVOS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo proyecto
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                        Asignar encargado
                                        </button>';
                                    }
                                    ?>
                                </h4>
                                <a href="proyectosfinalizados.php" class="btn btn-primary btn-sm" id="floatingButton">
                                    Proyectos<br>finalizados
                                </a>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Cliente</th>
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                echo '<th>Presupuesto</th>';
                                            }
                                            ?>
                                            <th>Fecha inicio</th>
                                            <th>Fecha fin</th>
                                            <th>Prioridad</th>
                                            <th>Etapa</th>
                                            <th>Detalles</th>
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                echo '<th>Encargado(s) de proyecto</th>';
                                            }
                                            ?>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [5, 9])) {
                                        //     $query = "SELECT proyecto.*
                                        //         FROM proyecto 
                                        //         JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
                                        //         JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo
                                        //         WHERE encargadoproyecto.codigooperador = $codigo 
                                        //         AND proyecto.estatus = 1
                                        //         ORDER BY proyecto.prioridad ASC";
                                        // } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        $query = "SELECT * FROM proyecto WHERE estatus = 1 OR estatus = 2 ORDER BY prioridad ASC";
                                        //}
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
                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                        echo '<td><p>$' . $registro['presupuesto'] . '</p></td>';
                                                    }
                                                    ?>
                                                    <td>
                                                        <p><?= $registro['fechainicio']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['fechafin']; ?></p>
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

                                                    <td style="cursor: all-scroll;">
                                                        <p><?php
                                                            if ($registro['etapa'] === '6') {
                                                                echo "Recepción de PO";
                                                            } else if ($registro['etapa'] === '7') {
                                                                echo "Kick off meeting";
                                                            } else if ($registro['etapa'] === '8') {
                                                                echo "Visita formal de levantamiento";
                                                            } else if ($registro['etapa'] === '9') {
                                                                echo "Prediseño (mecánico y eléctrico)";
                                                            } else if ($registro['etapa'] === '10') {
                                                                echo "Revisión de diseño/aprobación de cliente";
                                                            } else if ($registro['etapa'] === '11') {
                                                                echo "Actualización de BOM";
                                                            } else if ($registro['etapa'] === '12') {
                                                                echo "Colocación de PO's";
                                                            } else if ($registro['etapa'] === '13') {
                                                                echo "Construcción del equipo";
                                                            } else if ($registro['etapa'] === '14') {
                                                                echo "Pruebas internas iniciales";
                                                            } else if ($registro['etapa'] === '15') {
                                                                echo "Debugging interno y pruebas secundarias";
                                                            } else if ($registro['etapa'] === '16') {
                                                                echo "Buf off interno";
                                                            } else if ($registro['etapa'] === '17') {
                                                                echo "Buy off con cliente";
                                                            } else if ($registro['etapa'] === '18') {
                                                                echo "Empaque y envío a instalaciones de cliente";
                                                            } else if ($registro['etapa'] === '19') {
                                                                echo "Instalación con cliente";
                                                            } else if ($registro['etapa'] === '20') {
                                                                echo "Arranque y validación de equipo (buy off)";
                                                            } else if ($registro['etapa'] === '21') {
                                                                echo "Entrenamiento";
                                                            } else {
                                                                echo "Asigne una etapa manualmente";
                                                            }
                                                            ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['detalles']; ?></p>
                                                    </td>

                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                    ?>
                                                        <td>
                                                            <?php
                                                            $queryAsignacion = "SELECT encargadoproyecto.*, encargadoproyecto.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo
                                                        FROM encargadoproyecto
                                                        JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo 
                                                        WHERE encargadoproyecto.idproyecto = " . $registro['id'];
                                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                                foreach ($query_run_asignacion as $asignacion) {
                                                            ?>

                                                                    <form class="deleteForm" action="codencargados.php" method="post">
                                                                        <div style="display: flex; align-items: center;">
                                                                            <p style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>
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
                                                        <a href="editarproyecto.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                            echo '<form action="codeproyecto.php" method="POST" class="d-inline">
                                                                        <button type="submit" name="archivar" value="' . $registro['id'] . '" class="btn btn-danger btn-sm m-1"><i class="bi bi-x-circle"></i></button>
                                                                    </form>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='12'><p>No se encontro ningun registro</p></td>";
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PROYECTO</h1>
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

                            <div class="form-floating mt-3">
                                <input type="text" class="form-control" name="presupuesto" id="presupuesto" placeholder="Presupuesto" autocomplete="off" required>
                                <label style="margin-left: 0px !important;" for="presupuesto">Presupuesto</label>
                            </div>

                            <div class="form-floating mt-3">
                                <select class="form-select" name="prioridad" id="prioridad" autocomplete="off" required>
                                    <option value="1" selected>Seleccione un nivel de prioridad</option>
                                    <option value="1">Prioridad 1</option>
                                    <option value="2">Prioridad 2</option>
                                    <option value="3">Prioridad 3</option>
                                    <option value="4">Prioridad 4</option>
                                    <option value="5">Prioridad 5</option>
                                    <option value="6">Prioridad 6</option>
                                    <option value="7">Prioridad 7</option>
                                    <option value="8">Prioridad 8</option>
                                    <option value="9">Prioridad 9</option>
                                    <option value="10">Prioridad 10</option>
                                    <option value="11">Prioridad 11</option>
                                    <option value="12">Prioridad 12</option>
                                    <option value="13">Prioridad 13</option>
                                    <option value="14">Prioridad 14</option>
                                    <option value="15">Prioridad 15</option>
                                    <option value="16">Prioridad 16</option>
                                    <option value="17">Prioridad 17</option>
                                    <option value="18">Prioridad 18</option>
                                    <option value="19">Prioridad 19</option>
                                    <option value="20">Prioridad 20</option>
                                    <option value="21">Prioridad 21</option>
                                    <option value="22">Prioridad 22</option>
                                    <option value="23">Prioridad 23</option>
                                    <option value="24">Prioridad 24</option>
                                    <option value="25">Prioridad 25</option>
                                    <option value="26">Prioridad 26</option>
                                    <option value="27">Prioridad 27</option>
                                    <option value="28">Prioridad 28</option>
                                    <option value="29">Prioridad 29</option>
                                    <option value="30">Prioridad 30</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="prioridad">Prioridad del proyecto</label>
                            </div>

                            <!-- <div class="form-floating mt-3 mt-3" hidden>
                                <select class="form-select" name="etapadiseño" id="etapadiseño" autocomplete="off" required>
                                    <option disabled>Seleccione una etapa</option>
                                    <option selected value="1">Diseño</option>
                                    <option value="2">Revisión interna</option>
                                    <option value="3">Revisión con cliente</option>
                                    <option value="4">Planos</option>
                                    <option value="5">Bom</option>
                                    <option value="6">Manufactura</option>
                                    <option value="7">Remediación</option>
                                    <option value="8">Documentación</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapadiseño">Etapa de diseño:</label>
                            </div>

                            <div class="form-floating mt-3 mt-3" hidden>
                                <select class="form-select" name="etapacontrol" id="etapacontrol" autocomplete="off" required>
                                    <option disabled>Seleccione una etapa</option>
                                    <option selected value="1">Diseño</option>
                                    <option value="2">Revisión interna</option>
                                    <option value="3">Revisión con cliente</option>
                                    <option value="4">Diagramas</option>
                                    <option value="5">Bom</option>
                                    <option value="6">Manufactura</option>
                                    <option value="7">Programación</option>
                                    <option value="8">Debugging</option>
                                    <option value="9">Documentación</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapacontrol">Etapa de control:</label>
                            </div>

                            <div class="form-floating mt-3 mt-3" hidden>
                                <select class="form-select" name="etapatcontrol" id="etapatcontrol" autocomplete="off" required hidden>
                                    <option disabled>Seleccione una etapa</option>
                                    <option selected value="1">Revisión BOM controles</option>
                                    <option value="2">Armado de tableros de control</option>
                                    <option value="3">Pruebas electricas y de comunicación</option>
                                    <option value="4">Remediación</option>
                                    <option value="5">Ensamble en maquinaria</option>
                                    <option value="6">Ruteo final</option>
                                    <option value="7">Etiquetado</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapatcontrol">Etapa ensamble técnico control:</label>
                            </div>

                            <div class="form-floating mt-3 mt-3" hidden>
                                <select class="form-select" name="etapamecanica" id="etapamecanica" autocomplete="off" required>
                                    <option disabled>Seleccione una etapa</option>
                                    <option selected value="1">Revisión BOM mecánico</option>
                                    <option value="2">Armado de componentes mecánicos</option>
                                    <option value="3">Pruebas de ensamble</option>
                                    <option value="4">Remediación</option>
                                    <option value="5">Desensamble para acabados</option>
                                    <option value="6">Armado final</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapamecanica">Etapa ensamble mecánica/neumatica:</label>
                            </div> -->

                            <div class="form-floating mt-3 mt-3">
                                <select class="form-select" name="etapamecanica" id="etapamecanica" autocomplete="off" required>
                                    <option disabled>Seleccione una etapa</option>
                                    <option disabled>------- Ejecución -------</option>
                                    <option selected value="6">Recepción de PO</option>
                                    <option value="7">Kick off meeting</option>
                                    <option value="8">Visita formal de levantamiento</option>
                                    <option value="9">Prediseño (mecánico y eléctrico)</option>
                                    <option value="10">Revisión de diseño/aprobación con cliente</option>
                                    <option value="11">Actualización de BOM</option>
                                    <option value="12">Colocación de PO's</option>
                                    <option value="13">Construcción del equipo</option>
                                    <option value="14">Pruebas internas iniciales</option>
                                    <option value="15">Debugging interno y pruebas secundarias</option>
                                    <option value="16">Buf off interno</option>
                                    <option value="17">Buy off con cliente</option>
                                    <option value="18">Empaque y envío a instalaciones de cliente</option>
                                    <option disabled>------- Validación -------</option>
                                    <option value="19">Instalación con cliente</option>
                                    <option value="20">Arranque y validación de equipo (buy off)</option>
                                    <option value="21">Entrenamiento</option>
                                </select>
                                <label style="margin-left: 0px !important;" for="etapamecanica">Etapa de proyecto:</label>
                            </div>

                            <div class="form-floating mt-3">
                                <input type="date" class="form-control" name="fechainicio" id="fechainicio" placeholder="Fecha de inicio" autocomplete="off" required>
                                <label style="margin-left: 0px !important;" for="fechainicio">Fecha de inicio</label>
                            </div>

                            <div class="form-floating mt-3">
                                <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="Fecha de finalizacion" autocomplete="off" required>
                                <label style="margin-left: 0px !important;" for="fechafin">Fecha de finalizacion</label>
                            </div>

                            <div class="form-floating mt-3">
                                <textarea class="form-control" placeholder="Detalles" id="detalles" name="detalles" style="height: 150px" required></textarea>
                                <label style="margin-left: 0px !important;" for="detalles">Detalles del proyecto:</label>
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
                                        echo "<option value='$idProyecto' " . ($registro['id'] == $idProyecto ?: '') . ">$opcion</option>";
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

                                    // Cambio en el nombre del campo para que se envíen como un array
                                    echo "<input  style='margin-right: 10px;' class='form-check-inputmb-2' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                    echo "<label class='form-check-label mb-2' for='codigooperador_$idUsuario'><img style='width:40px;' src='$idMedio' alt=''> $nombreCompleto</label><br>";
                                }
                            }
                            ?>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="proyecto">Guardar</button>
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
        });

        document.getElementById('miFormulario').addEventListener('submit', function(event) {
            // Obtener todos los checkboxes con name 'codigooperador[]'
            const checkboxes = document.querySelectorAll('input[name="codigooperador[]"]');

            // Verificar si al menos uno está marcado
            let alMenosUnoMarcado = false;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    alMenosUnoMarcado = true;
                }
            });

            // Si ningún checkbox está marcado, evita el envío del formulario
            if (!alMenosUnoMarcado) {
                alert('Por favor, seleccione al menos un usuario encargado.');
                event.preventDefault(); // Evita que el formulario se envíe
            }
        });
    </script>
</body>

</html>