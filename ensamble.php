<?php
session_start();
require 'dbcon.php';

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
    <title>Ensamble | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
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
                                <h4>ENSAMBLE
                                    <button type="button" class="btn btn-secondary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                        Asignar T. Control
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Asignar T. Mecánico
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <?php include('message.php'); ?>
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Proyecto</th>
                                            <th>Prioridad</th>
                                            <th>Etapa de ensamble mecanica/neumatica</th>
                                            <th>Etapa de ensamble T.Control</th>
                                            <th>Técnico mecanico asiganado(s)</th>
                                            <th>Técnico control asiganado(s)</th>
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
                                                    <td>
                                                        <p class="text-center"><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['nombre']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-center"><?= $registro['prioridad']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?php
                                                            if ($registro['etapamecanica'] === '1') {
                                                                echo "Revisión BOM mecánico";
                                                            } else if ($registro['etapamecanica'] === '2') {
                                                                echo "Armado de componentes mecánicos";
                                                            } else if ($registro['etapamecanica'] === '3') {
                                                                echo "Pruebas de ensamble";
                                                            } else if ($registro['etapamecanica'] === '4') {
                                                                echo "Remediación";
                                                            } else if ($registro['etapamecanica'] === '5') {
                                                                echo "Desensamble para acabados";
                                                            } else if ($registro['etapamecanica'] === '6') {
                                                                echo "Armado final";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?php
                                                            if ($registro['etapatcontrol'] === '1') {
                                                                echo "Revisión BOM controles";
                                                            } else if ($registro['etapatcontrol'] === '2') {
                                                                echo "Armado de tableros de control";
                                                            } else if ($registro['etapatcontrol'] === '3') {
                                                                echo "Pruebas electrícas y de comunicación";
                                                            } else if ($registro['etapatcontrol'] === '4') {
                                                                echo "Remediación";
                                                            } else if ($registro['etapatcontrol'] === '5') {
                                                                echo "Ensamble en maquinaria";
                                                            } else if ($registro['etapatcontrol'] === '6') {
                                                                echo "Ruteo final";
                                                            } else if ($registro['etapatcontrol'] === '7') {
                                                                echo "Etiquetado";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></p>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        // Consulta para obtener los registros de encargadomecanico con el nombre completo
                                                        $queryAsignacion = "SELECT encargadomecanico.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom
                                                        FROM encargadomecanico
                                                        JOIN usuarios ON encargadomecanico.codigooperador = usuarios.codigo
                                                        WHERE encargadomecanico.idproyecto = " . $registro['id'];

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
                                                        <?php
                                                        // Consulta para obtener los registros de encargadotcontrol con el nombre completo
                                                        $queryAsignacion = "SELECT encargadotcontrol.*, usuarios.nombre, usuarios.apellidop, usuarios.apellidom
                                                        FROM encargadotcontrol
                                                        JOIN usuarios ON encargadotcontrol.codigooperador = usuarios.codigo
                                                        WHERE encargadotcontrol.idproyecto = " . $registro['id'];

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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR TÉCNICO MECÁNICO</h1>
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
                            $query = "SELECT * FROM usuarios WHERE rol=3";
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
                            <button type="submit" class="btn btn-primary" name="mecanico">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2-->
    <div class="modal fade" id="exampleModalDos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR TÉCNICO DE CONTROL</h1>
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
                            $query = "SELECT * FROM usuarios WHERE rol=4";
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
                            <button type="submit" class="btn btn-primary" name="control">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [2, "asc"]
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