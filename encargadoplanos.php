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
    <title>Operadores Asignados a Maquinados | Solara</title>
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
                                <h4>OPERADORES ASIGNADOS A MAQUINADOS 
                                <button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Asignar maquinado
                                </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <?php include('message.php'); ?>
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Plano</th>
                                            <th>Operador asignado</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT asignacionplano.*, plano.id, asignacionplano.id AS id_encargado, plano.nombreplano, usuarios.nombre AS username, usuarios.apellidop, usuarios.apellidom
                                        FROM asignacionplano
                                        JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                                        JOIN plano ON asignacionplano.idplano = plano.id 
                                        WHERE asignacionplano.codigooperador = usuarios.codigo
                                        ORDER BY id_encargado desc";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p class="text-center"><?= $registro['id_encargado']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['nombreplano']; ?></p>
                                                    </td>
                                                    <td>
                                                    <p><?= $registro['username']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></p>
                                                    </td>
                                                    <td>
                                                        <form action="codencargados.php" method="POST" class="d-inline">
                                                            <button type="submit" name="deleteplano" value="<?= $registro['id_encargado']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">ASIGNAR MAQUINADO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codetecnicos.php" method="POST" class="row">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="idplano" id="idplano">
                                <option disabled selected>Seleccione un plano</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM plano WHERE estatusplano = 1";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($plano = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del plano
                                        $opcion = $plano['nombreplano'];
                                        // Obtener el ID del usuario
                                        $idPlano = $plano['id'];
                                        // Mostrar la opción con el valor igual al ID del plano
                                        echo "<option value='$idPlano' " . ($registro['id'] == $idPlano ? 'selected' : '') . ">$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idplano">Plano a asociar</label>
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
                            <button type="submit" class="btn btn-primary" name="plano">Guardar</button>
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
                    [0, "desc"]
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