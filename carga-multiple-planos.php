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
    <title>Carga múltiple de planos | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
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
                    <div class="col-12 p-5 mb-3" style="background-color: #3c59c0ff;border:3px solid #2c3862ff;border-radius:10px;">
                        <label for="pdfFiles" class="form-label text-light">Subir varios archivos PDF</label>
                        <input
                            class="form-control"
                            type="file"
                            id="pdfFiles"
                            accept="application/pdf"
                            multiple>
                    </div>
                    <form action="codemaquinados.php" method="POST" class="row mb-0" enctype="multipart/form-data">
                        <div class="col-12 p-3 mt-3" id="filaPlano" style="background-color: #e6e6e6ff;border-radius:10px;">
                            <h1 class="modal-title fs-5" id="tituloPlano">NUEVO PLANO</h1>
                            <div class="row mb-0">
                                <div class="col-7 mb-3">
                                    <div class="row">
                                        <div class="form-floating col-4 mt-3">
                                            <select class="form-select" name="idproyecto[]" id="idproyecto">
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

                                        <div class="form-floating mt-3 col-8">
                                            <input type="text" class="form-control" name="nombreplano[]" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                                            <label for="nombreplano" id="nombrePlano">Nombre del plano</label>
                                        </div>

                                        <div id="planoElements">
                                            <label for="medio" class="form-label">Plano PDF</label>
                                            <input class="form-control" type="file" id="medio" name="medio[]" max="100000">
                                        </div>

                                        <div class="form-floating mt-3" id="actividadElements" style="display: none;">
                                            <input type="text" class="form-control" name="actividad[]" id="actividad" placeholder="Actividad" autocomplete="off">
                                            <label for="actividad">Detalles de la actividad</label>
                                        </div>

                                        <div class="form-floating mt-3 col-4">
                                            <input type="text" class="form-control" name="piezas[]" id="piezas" placeholder="Piezas" autocomplete="off" required>
                                            <label for="piezas">Número de piezas</label>
                                        </div>

                                        <div class="form-floating mt-3 col-8">
                                            <select class="form-select" name="nivel[]" id="nivel" autocomplete="off" required>
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
                                    <?php $i = 0; // índice fila 
                                    ?>
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

                                                echo "<input class='form-check-input mb-2' type='checkbox' id='codigooperador_{$idUsuario}_{$i}' name='codigooperador[{$i}][]' value='{$idUsuario}'>";
                                                echo "<label class='form-check-label mb-2' for='codigooperador_{$idUsuario}_{$i}'><img style='width:40px;' src='{$idMedio}' alt=''> {$nombreCompleto}</label><br>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 p-5 text-center">
                            <button type="submit" class="btn btn-primary" name="savemulti">Guardar</button>
                            <a href="maquinados.php" class="text-dark m-2">Regresar</a>
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Índice para filas clonadas (la inicial es 0)
            let filaIndex = 0;

            // Manejar cambio en el primer select de proyecto para preguntar si asignar a todos
            const primerSelectProyecto = document.querySelector("#filaPlano #idproyecto");
            if (primerSelectProyecto) {
                primerSelectProyecto.addEventListener("change", function() {
                    const valorSeleccionado = this.value;

                    Swal.fire({
                        title: '¿Asignar a todos?',
                        text: "¿Deseas asignar este mismo proyecto a todos los planos?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, asignar a todos',
                        cancelButtonText: 'No, solo este',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Seleccionar todos los selects con name="idproyecto[]"
                            document.querySelectorAll('select[name="idproyecto[]"]').forEach(select => {
                                select.value = valorSeleccionado;
                            });
                            Swal.fire(
                                'Asignado',
                                'Se asignó el mismo proyecto a todos los planos.',
                                'success'
                            );
                        }
                    });
                });
            }

            // Manejar selección de archivos PDF
            document.getElementById('pdfFiles').addEventListener('change', function() {
                const filaBase = document.getElementById('filaPlano');
                const form = filaBase.closest('form');
                const botonesDiv = form.querySelector('.col-12.p-5.text-center');

                // Limpiar filas clonadas previas
                form.querySelectorAll('.fila-plano-extra').forEach(el => el.remove());

                const files = Array.from(this.files);

                files.forEach((file, index) => {
                    let filaActual;

                    if (index === 0) {
                        filaActual = filaBase;
                        filaIndex = 0;
                    } else {
                        filaIndex = index;
                        filaActual = filaBase.cloneNode(true);
                        filaActual.classList.add('fila-plano-extra');
                        filaActual.id = `filaPlano_${filaIndex + 1}`;

                        // Insertar antes de los botones
                        form.insertBefore(filaActual, botonesDiv);
                    }

                    // Asignar archivo al input file
                    let inputPDF = filaActual.querySelector('input[name="medio[]"]');
                    if (inputPDF) {
                        let dt = new DataTransfer();
                        dt.items.add(file);
                        inputPDF.files = dt.files;
                    }

                    // Nombre del archivo sin extensión en input nombreplano[]
                    let nombrePlanoInput = filaActual.querySelector('input[name="nombreplano[]"]');
                    if (nombrePlanoInput) {
                        nombrePlanoInput.value = file.name.replace(/\.pdf$/i, '');
                    }

                    // Actualizar todos los atributos name e id para que tengan el índice correcto

                    // Select proyecto
                    let selectProyecto = filaActual.querySelector('select[name="idproyecto[]"]');
                    if (selectProyecto) {
                        selectProyecto.id = `idproyecto_${filaIndex}`;
                        // IMPORTANTÍSIMO: aquí asignamos valor vacío para que no quede "Seleccione un proyecto" seleccionado y no envíe valor
                        selectProyecto.value = "";
                        // Si quieres asignar otro valor por default, cambia la línea anterior a:
                        // selectProyecto.value = "32"; // o cualquier id de proyecto válido
                    }

                    // Inputs nombreplano, actividad, piezas, nivel
                    ['nombreplano', 'actividad', 'piezas', 'nivel'].forEach(campo => {
                        let input = filaActual.querySelector(`[name="${campo}[]"]`);
                        if (input) {
                            input.id = `${campo}_${filaIndex}`;
                            // Si quieres limpiar los campos clonados (excepto nombreplano que ya tiene valor):
                            if (campo !== 'nombreplano') input.value = "";
                        }
                    });

                    // Input file medio
                    if (inputPDF) {
                        inputPDF.id = `medio_${filaIndex}`;
                    }

                    // Checkboxes operadores
                    const checkboxes = filaActual.querySelectorAll('input[type="checkbox"][name^="codigooperador"]');
                    checkboxes.forEach(cb => {
                        const parts = cb.id.split('_'); // ejemplo: codigooperador_123_0
                        const idUsuario = parts[1];
                        cb.name = `codigooperador[${filaIndex}][]`;
                        cb.id = `codigooperador_${idUsuario}_${filaIndex}`;
                        cb.checked = false; // limpiar selección
                        // Actualizar label for
                        const label = filaActual.querySelector(`label[for="${parts.join('_')}"]`);
                        if (label) label.htmlFor = cb.id;
                    });

                    // Agregar botón eliminar si no existe
                    if (!filaActual.querySelector('.btn-delete-fila')) {
                        const btnDelete = document.createElement('button');
                        btnDelete.type = 'button';
                        btnDelete.className = 'btn btn-danger btn-sm btn-delete-fila mt-2';
                        btnDelete.textContent = 'Eliminar';
                        filaActual.appendChild(btnDelete);
                    }
                });
            });

            // Delegación de eventos para botón eliminar fila
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-delete-fila')) {
                    const fila = e.target.closest('#filaPlano, .fila-plano-extra');
                    if (fila) {
                        if (fila.id === 'filaPlano') {
                            // Limpiar fila base
                            fila.querySelectorAll('input, select').forEach(input => {
                                if (input.type === 'file') {
                                    input.value = null;
                                } else if (input.type === 'checkbox') {
                                    input.checked = false;
                                } else {
                                    input.value = '';
                                }
                            });
                        } else {
                            fila.remove();
                        }
                    }
                }
            });

        });
    </script>


</body>

</html>