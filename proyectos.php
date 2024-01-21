<?php
session_start();
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
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
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
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>PROYECTOS
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo proyecto
                                        </button>';
                                    }
                                    ?>
                                    <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                        Asignar encargado
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
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
                                        $query = "SELECT * FROM proyecto ORDER BY prioridad ASC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><p class="text-center"><?= $registro['id']; ?></p></td>
                                                    <td><p class="text-center"><?= $registro['nombre']; ?></p></td>
                                                    <td><p class="text-center"><?= $registro['cliente']; ?></p></td>
                                                    <td style="min-width: 250px;">
                                                        <p><b>Presupuesto: </b>$<?= $registro['presupuesto']; ?></p>
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
                                                            } else if ($registro['etapadiseño'] === '6') {
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
                                                        // Consulta para obtener los registros de encargadoproyecto con el nombre completo
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
                                                        <?php
                                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                                            echo '<form action="codeproyecto.php" method="POST" class="d-inline">
                                                                        <button type="submit" name="delete" value="' . $registro['id'] . '" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                                    </form>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td><p>No se encontro ningun registro</p></td><td></td><td></td>";
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PROYECTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codeproyecto.php" method="POST" class="row">
                        <div class="form-floating col-12">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre del proyecto</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="text" class="form-control" name="cliente" id="cliente" placeholder="Nombre" autocomplete="off" required>
                            <label for="cliente">Cliente</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="text" class="form-control" name="presupuesto" id="presupuesto" placeholder="Presupuesto" autocomplete="off" required>
                            <label for="presupuesto">Presupuesto</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
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
                            <label for="prioridad">Prioridad del proyecto</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mt-3" hidden>
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
                            <label for="etapadiseño">Etapa de diseño:</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mt-3" hidden>
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
                            <label for="etapacontrol">Etapa de control:</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mt-3" hidden>
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
                            <label for="etapatcontrol">Etapa ensamble técnico control:</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mt-3" hidden>
                            <select class="form-select" name="etapamecanica" id="etapamecanica" autocomplete="off" required>
                                <option disabled>Seleccione una etapa</option>
                                <option selected value="1">Revisión BOM mecánico</option>
                                <option value="2">Armado de componentes mecánicos</option>
                                <option value="3">Pruebas de ensamble</option>
                                <option value="4">Remediación</option>
                                <option value="5">Desensamble para acabados</option>
                                <option value="6">Armado final</option>
                            </select>
                            <label for="etapamecanica">Etapa ensamble mecánica/neumatica:</label>
                        </div>

                        <div class="form-floating col-12 col-md-6 mt-3">
                            <input type="date" class="form-control" name="fechainicio" id="fechainicio" placeholder="Fecha de inicio" autocomplete="off" required>
                            <label for="fechainicio">Fecha de inicio</label>
                        </div>

                        <div class="form-floating col-12 col-md-6 mt-3">
                            <input type="date" class="form-control" name="fechafin" id="fechafin" placeholder="Fecha de finalizacion" autocomplete="off" required>
                            <label for="fechafin">Fecha de finalizacion</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <textarea class="form-control" placeholder="Detalles" id="detalles" name="detalles" style="height: 150px" required></textarea>
                            <label for="detalles">Detalles del proyecto:</label>
                        </div>

                        <div class="form-check col-12 mt-3 m-3">
                            <?php
                            // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                            $query = "SELECT * FROM usuarios WHERE rol IN (5,9)";
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

    <!-- Modal 2 -->
    <div class="modal fade" id="exampleModalDos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR ENCARGADO DE PROYECTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="miFormulario" action="codetecnicos.php" method="POST" class="row">
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
                            <label for="idproyecto">Proyecto a asignar</label>
                        </div>

                        <div class="form-check col-12 m-3">
                            <?php
                            // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                            $query = "SELECT * FROM usuarios WHERE rol IN (5,9)";
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
                            <button type="submit" class="btn btn-primary" name="proyecto">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [4, "asc"]
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