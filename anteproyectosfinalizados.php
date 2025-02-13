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
    <title>Proyectos finalizados | Solara</title>
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
                                <h4>ANTEPROYECTOS ARCHIVADOS</h4>
                                <a href="anteproyectos.php" class="btn btn-primary btn-sm" id="floatingButton">
                                    Anteproyectos<br>activos
                                </a>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Anteroyecto</th>
                                            <th>Cliente</th>
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
                                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [5, 9, 13])) {
                                            $query = "SELECT proyecto.*
                                                FROM proyecto 
                                                JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
                                                JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo
                                                WHERE encargadoproyecto.codigooperador = $codigo 
                                                AND proyecto.estatus = 3
                                                ORDER BY proyecto.prioridad ASC";
                                        } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                        $query = "SELECT * FROM proyecto WHERE estatus = 3 ORDER BY prioridad ASC";
                                        }
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