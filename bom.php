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

//Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'BOM' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>BOM | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
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
                                            <button type="button" class="btn btn-sm btn-success float-end m-1" onclick="generarPDF()">Propuesta</button>
                                        </form>
                                        <button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            Nuevo BOM
                                        </button>

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
                                            <th>Piezas</th>
                                            <th>Proveedor</th>
                                            <th>Descripcion</th>
                                            <th>Marca</th>
                                            <th>Condicion</th>
                                            <th>Costo unitario</th>
                                            <th>Costo total</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM bom ORDER BY id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><input type="checkbox" name="seleccionados[]" value="<?= $registro['id']; ?>"></td>
                                                    <td><?= $registro['id']; ?></td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td><input type="text" class="form-control piezas-container" name="piezas[]" id="piezasSeleccionadas_<?= $registro['id']; ?>" placeholder="Cantidad" autocomplete="off"></td>
                                                    <td><?= $registro['proveedor']; ?></td>
                                                    <td><?= $registro['descripcion']; ?></td>
                                                    <td><?= $registro['marca']; ?></td>
                                                    <td><?= $registro['condicion']; ?></td>
                                                    <td>$<?= $registro['costo']; ?></td>
                                                    <td><input type="text" class="form-control costoTotal" name="costoTotal[]" id="costoTotal_<?= $registro['id']; ?>" readonly></td>
                                                    <td>
                                                        <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1 deletebtn"><i class="bi bi-trash-fill"></i></button>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='11'><p>No se encontró ningún registro</p></td></tr>";
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO BOM</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codebom.php" method="POST" class="row">
                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre</label>
                        </div>

                        <div class="form-floating col-6 mt-3">
                            <input type="text" class="form-control" name="proveedor" id="proveedor" placeholder="Proveedor" autocomplete="off" required>
                            <label for="proveedor">Proveedor</label>
                        </div>

                        <div class="form-floating col-6 mt-3">
                            <select class="form-select" name="condicion" id="condicion" autocomplete="off" required>
                                <option selected disabled>Seleccione una opcion</option>
                                <option value="Nuevo">Nuevo</option>
                                <option value="Usado">Usado</option>
                            </select>
                            <label for="condicion">Condición</label>
                        </div>

                        <div class="form-floating col-7 mt-3">
                            <input type="text" class="form-control" name="marca" id="marca" placeholder="Marca" autocomplete="off" required>
                            <label for="marca">Marca</label>
                        </div>

                        <div class="form-floating col-5 mt-3">
                            <input type="text" class="form-control" name="costo" id="costo" placeholder="Costo" autocomplete="off" required>
                            <label for="costo">Costo unitario</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción" style="min-height: 150px;"></textarea>
                            <label for="descripcion" class="form-label">Descripción</label>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            // Ocultar inicialmente los campos de piezas y costoTotal
            $('.piezas-container, .costoTotal').hide();

            // Manejar el cambio en el checkbox
            $('input[name="seleccionados[]"]').change(function() {
                var isChecked = $(this).is(':checked');
                var $row = $(this).closest('tr');
                var $piezasInput = $row.find('.piezas-container');
                var $costoTotalInput = $row.find('.costoTotal');

                // Mostrar u ocultar los campos según el estado del checkbox
                if (isChecked) {
                    $piezasInput.show();
                    $costoTotalInput.show();
                } else {
                    $piezasInput.hide();
                    $costoTotalInput.hide();
                    $piezasInput.val(''); // Limpiar el valor del input de piezas
                    $costoTotalInput.val(''); // Limpiar el valor del input de costoTotal
                }
            });

            // Manejar el evento 'input' en los campos de piezas para calcular el costo total
            $('.piezas-container').on('input', function() {
                var $row = $(this).closest('tr');
                var $costoUnitario = parseFloat($row.find('td:nth-child(9)').text().replace('$', ''));
                var piezas = parseFloat($(this).val());
                var costoTotal = isNaN($costoUnitario) || isNaN(piezas) ? 0 : $costoUnitario * piezas;
                $row.find('.costoTotal').val('$' + costoTotal.toFixed(2)); // Actualizar el valor del campo de costoTotal
            });
        });

        function generarPDF() {
            var seleccionados = document.getElementsByName('seleccionados[]');
            var idsSeleccionados = [];

            for (var i = 0; i < seleccionados.length; i++) {
                if (seleccionados[i].checked) {
                    var id = seleccionados[i].value;
                    var piezas = document.getElementById('piezasSeleccionadas_' + id).value;
                    var costoTotal = document.getElementById('costoTotal_' + id).value;

                    console.log("ID:", id);
                    console.log("Piezas:", piezas);
                    console.log("Costo Total:", costoTotal);

                    var item = {
                        id: id,
                        piezas: piezas,
                        costoTotal: costoTotal
                    };
                    idsSeleccionados.push(item);
                }
            }

            if (idsSeleccionados.length > 0) {
                document.getElementById('idsSeleccionados').value = JSON.stringify(idsSeleccionados);
                document.getElementById('generarPDFForm').submit();
            } else {
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
                ]
            });
        });

        const deleteButtons = document.querySelectorAll('.deletebtn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                const id = e.target.value; // Obtener el valor del botón delete

                // Mostrar la alerta de SweetAlert2 para confirmar la eliminación
                Swal.fire({
                    title: '¿Estás seguro que deseas eliminar este registro?',
                    text: '¡No podrás deshacer esta acción!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('delete', id);
                        fetch('codebom.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);

                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>