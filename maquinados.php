<?php
session_start();
require 'dbcon.php';
header('Content-Type: text/html; charset=UTF-8');

//Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
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
    <title>Maquinados | Solara</title>
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
                                <h4>MAQUINADOS
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])){
                                    echo'<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Nuevo plano
                                </button>';}
                                ?>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <?php include('message.php'); ?>
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [8])) {
                                ?>
                                    <table id="miTablaTres" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Proyecto</th>
                                                <th>Planos asociados</th>
                                                <th>Operadores asignados</th>
                                                <th>Número de piezas</th>
                                                <th>Prioridad</th>
                                                <th>Nivel de pieza</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT proyecto.*, plano.*
                                            FROM plano 
                                            JOIN proyecto ON plano.idproyecto = proyecto.id 
                                            JOIN asignacionplano ON asignacionplano.idplano = plano.id 
                                            JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                            WHERE asignacionplano.codigooperador = $codigo 
                                            AND (plano.estatusplano = 1 OR plano.estatusplano = 2)
                                            ORDER BY proyecto.prioridad ASC";

                                            $query_run = mysqli_query($con, $query);

                                            if (mysqli_num_rows($query_run) > 0) {
                                                foreach ($query_run as $registro) {
                                            ?>
                                                    <tr>
                                                        <td><?= $registro['nombre']; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Plano <?= $registro['nombreplano']; ?></button>
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
                                                        </td>
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
                                                                echo 'No asignado';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= $registro['piezas']; ?></td>
                                                        <td><?= $registro['prioridad']; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($registro['nivel'] === '1') {
                                                                echo "Nivel 1";
                                                            } else if ($registro['nivel'] === '2') {
                                                                echo "Nivel 2";
                                                            } else if ($registro['nivel'] === '3') {
                                                                echo "Nivel 3";
                                                            } else if ($registro['nivel'] === '4') {
                                                                echo "Nivel 4";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                        <?php
                                                        $id = $registro['id'];
                                                            if ($registro['estatusplano'] === '1') {
                                                                echo '<a href="inicioactividades.php?id='. $id .'" class="btn btn-success btn-sm m-1">Iniciar</a>';
                                                            } else if ($registro['estatusplano'] === '2') {
                                                                echo '<form action="codeactividad.php" method="post">
                                                                <input type="hidden" value="'. $id .'" name="id">
                                                                <button type="submit" name="restart" class="btn btn-sm btn-primary">Seguimiento</button>
                                                                </form>';
                                                            } else {
                                                                echo "Error, contacte a soporte";
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
                                <?php
                                } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 6, 7, 9])) {
                                ?>
                                    <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Proyecto</th>
                                                <th>Planos asociados</th>
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
                                        WHERE (plano.estatusplano = 1 OR plano.estatusplano = 2)
                                        ORDER BY proyecto.prioridad asc";
                                            $query_run = mysqli_query($con, $query);
                                            if (mysqli_num_rows($query_run) > 0) {
                                                foreach ($query_run as $registro) {
                                            ?>
                                                    <tr>
                                                        <td><?= $registro['nombre']; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Plano <?= $registro['nombreplano']; ?></button>
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
                                                        </td>
                                                        <td><?= $registro['piezas']; ?></td>
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
                                                                echo 'No asignado';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= $registro['prioridad']; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($registro['nivel'] === '1') {
                                                                echo "Nivel 1";
                                                            } else if ($registro['nivel'] === '2') {
                                                                echo "Nivel 2";
                                                            } else if ($registro['nivel'] === '3') {
                                                                echo "Nivel 3";
                                                            } else if ($registro['nivel'] === '4') {
                                                                echo "Nivel 4";
                                                            } else {
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="editarmaquinado.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                            <form action="codemaquinados.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo "<td><p>No se encontro ningun registro</p></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <h4 class="mt-5">MAQUINADOS FINALIZADOS</h4>
                                    <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Proyecto</th>
                                                <th>Planos asociados</th>
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
                                                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Plano <?= $registro['nombreplano']; ?></button>
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
                                                        </td>
                                                        <td><?= $registro['piezas']; ?></td>
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
                                                        <td><?= $registro['prioridad']; ?></td>
                                                        <td><?php
                                                            if ($registro['nivel'] === '1'){
                                                                echo "Nivel 1";
                                                            } elseif ($registro['nivel'] === '2'){
                                                                echo "Nivel 2";
                                                            } elseif ($registro['nivel'] === '3'){
                                                                echo "Nivel 3";
                                                            } elseif ($registro['nivel'] === '4'){
                                                                echo "Nivel 4";
                                                            } else{
                                                                echo "Error, contacte a soporte";
                                                            }
                                                            ?></td>
                                                        <td>
                                                            <a href="editarmaquinado.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                            <form action="codemaquinados.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo "<td><p>No se encontro ningun registro</p></td><td></td><td></td><td></td><td></td><td></td>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php
                                }
                                ?>
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PLANO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codemaquinados.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-12 mb-3">
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

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano">Nombre del plano</label>
                        </div>

                        <div class="mt-3">
                            <label for="medio" class="form-label">Plano PDF</label>
                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <!-- Incluir los archivos de PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('#miTabla, #miTablaDos, #miTablaTres').DataTable({
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

        // Al mostrar el modal, cargar el PDF en el iframe correspondiente
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($query_run as $registro) : ?>
                const pdfUrl<?= $registro['id']; ?> = '<?= $registro['medio']; ?>';
                const iframeId<?= $registro['id']; ?> = 'pdfViewer<?= $registro['id']; ?>';
                const modal<?= $registro['id']; ?> = new bootstrap.Modal(document.getElementById('pdfModal<?= $registro['id']; ?>'));

                modal<?= $registro['id']; ?>.addEventListener('shown.bs.modal', function() {
                    showPDF(pdfUrl<?= $registro['id']; ?>, iframeId<?= $registro['id']; ?>);
                });
            <?php endforeach; ?>
        });
    </script>
    


</body>

</html>