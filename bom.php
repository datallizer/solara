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
    <title>BOM | Solara</title>
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
                                <div class="row">
                                    <div class="col-6">
                                        <h4>BOM</h4>
                                    </div>
                                    <div class="col-6">
                                        <form id="generarPDFForm" action="generar_pdf.php" method="POST">
                                            <input type="hidden" id="idsSeleccionados" name="idsSeleccionados">
                                            <button type="button" class="btn btn-sm btn-success float-end" onclick="generarPDF()">Propuesta</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Material</th>
                                            <th>Proveedor</th>
                                            <th>Descripcion</th>
                                            <th>Marca</th>
                                            <th>Condicion</th>
                                            <th>Costo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM inventario ORDER BY id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><input type="checkbox" name="seleccionados[]" value="<?= $registro['id']; ?>"></td>
                                                    <td><?= $registro['id']; ?></td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td><?= $registro['proveedor']; ?></td>
                                                    <td><?= $registro['descripcion']; ?></td>
                                                    <td><?= $registro['marca']; ?></td>
                                                    <td><?= $registro['condicion']; ?></td>
                                                    <td>$<?= $registro['costo']; ?></td>
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
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
    function generarPDF() {
        var seleccionados = document.getElementsByName('seleccionados[]');
        var idsSeleccionados = [];
        for (var i = 0; i < seleccionados.length; i++) {
            if (seleccionados[i].checked) {
                idsSeleccionados.push(seleccionados[i].value);
            }
        }

        if (idsSeleccionados.length > 0) {
            // Convertir el array de IDs a una cadena para enviarlo como valor del campo oculto
            document.getElementById('idsSeleccionados').value = JSON.stringify(idsSeleccionados);

            // Enviar el formulario
            document.getElementById('generarPDFForm').submit();
        } else {
            // Mostrar SweetAlert2 en lugar de alert
            Swal.fire({
                title: 'Error',
                text: 'Por favor, seleccione al menos un material para generar la propuesta.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
    
    $(document).ready(function() {
        $('#miTabla, #miTablaDos').DataTable({
            "order": [
                [1, "asc"]
            ] // Ordenar la primera columna (índice 0) en orden descendente
        });
    });
</script>

</body>

</html>