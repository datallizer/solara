<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
header('Content-Type: text/html; charset=UTF-8');
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión
if (!empty($message)) {
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
    unset($_SESSION['message']);
}
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Compras finalizadas' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Compras finalizadas | Solara</title>
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
                    <div class="col-md-12 mt-3">
                        <a href="compras.php" class="btn btn-primary btn-sm" id="floatingButton">
                            Compras<br>pendientes
                        </a>
                        <div class="card">
                            <div class="card-header">
                                <h4><button id="generatePDF" class="btn btn-primary float-end">Generar PDF</button>
                                    COMPRAS FINALIZADAS</h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Solicitante</th>
                                            <th>Rol</th>
                                            <th>Proyecto</th>
                                            <th>PDF</th>
                                            <th>Notas</th>
                                            <th>Monto</th>
                                            <?php
                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 6])) {
                                                echo '<th>Acción</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT archivoquotes.*, proyecto.*, archivoquotes.id AS id_quote
                                            FROM archivoquotes 
                                            JOIN proyecto ON archivoquotes.proyecto = proyecto.id
                                            WHERE estatusq = 2
                                            ORDER BY archivoquotes.id ASC";


                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="select-row" data-id="<?= $registro['id_quote']; ?>" data-nombre="<?= $registro['nombre']; ?>" data-cotizacion="<?= $registro['cotizacion']; ?>" data-monto="<?= $registro['monto']; ?>" data-nota="<?= $registro['notas']; ?>">
                                                    </td>
                                                    <td><?= $registro['id_quote']; ?></td>
                                                    <td><?= $registro['solicitante']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($registro['rol'] === '1') {
                                                            echo "Administrador";
                                                        } else if ($registro['rol'] === '2') {
                                                            echo "Gerencia";
                                                        } else if ($registro['rol'] === '4') {
                                                            echo "Técnico controles";
                                                        } else if ($registro['rol'] === '5') {
                                                            echo "Ing. Diseño";
                                                        } else if ($registro['rol'] === '6') {
                                                            echo "Compras";
                                                        } else if ($registro['rol'] === '7') {
                                                            echo "Almacenista";
                                                        } else if ($registro['rol'] === '8') {
                                                            echo "Técnico mecanico";
                                                        } else if ($registro['rol'] === '9') {
                                                            echo "Ing. Control";
                                                        } else if ($registro['rol'] === '13') {
                                                            echo "Ing. Laser";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <a href="vercompra.php?id=<?= $registro['id_quote']; ?>" class="btn btn-outline-dark btn-sm">Cotizacion <?= $registro['cotizacion']; ?></a>
                                                    </td>
                                                    <td><?= $registro['notas']; ?></td>
                                                    <td>$<?= $registro['monto']; ?></td>

                                                    <?php
                                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 6])) {
                                                    ?>
                                                        <td>
                                                            <form action="codequotes.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="<?= $registro['id_quote']; ?>" class="btn btn-danger btn-sm m-1 deletebtn"><i class="bi bi-trash-fill deletebtn"></i></button>
                                                            </form>
                                                            <button type="button" value="<?= $registro['id_quote']; ?>" class="btn btn-warning btn-sm m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos<?= $registro['id_quote']; ?>"><i class="bi bi-pencil-square"></i></button>
                                                        </td>
                                                        <div class="modal fade" id="exampleModalDos<?= $registro['id_quote']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?= $registro['id_quote']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" style="text-transform: uppercase;" id="tituloPlanoDos<?= $registro['id_quote']; ?>">EDITAR <?= $registro['cotizacion']; ?></h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="codequotes.php" method="POST" class="row">
                                                                            <input type="hidden" name="id" value="<?= $registro['id_quote']; ?>" id="id">
                                                                            <input type="hidden" class="form-control" name="pasado" id="pasado<?= $registro['id_quote']; ?>" value="<?= $registro['monto']; ?>">
                                                                            <input type="hidden" class="form-control" name="cotizacion" id="cotizacion<?= $registro['id_quote']; ?>" value="<?= $registro['cotizacion']; ?>">

                                                                            <div class="form-floating col-12 mb-3">
                                                                                <input type="text" class="form-control" name="monto" id="monto<?= $registro['id_quote']; ?>" placeholder="Monto" value="<?= $registro['monto']; ?>">
                                                                                <label style="padding-left: 0px;" for="monto<?= $registro['id_quote']; ?>">Monto</label>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                                <button type="submit" class="btn btn-primary" name="updatemonto">Guardar</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>

                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'><p>No se encontró ningún registro</p></td></tr>";
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
    <!-- Incluir los archivos de PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <script>
     $(document).ready(function() {
    // Inicializamos DataTables
    const table = $('#miTablaDos').DataTable({
        "order": [
            [1, "desc"]
        ],
        "pageLength": 50
    });

    // Variable para saber si ya se ha aplicado el filtro
    let isFiltered = false;

    // Escuchar el evento de seleccionar un checkbox
    $(document).on('change', '.select-row', function() {
        const nombre = $(this).data('nombre');
        const isChecked = $(this).prop('checked');

        if (isChecked) {
            // Si el checkbox está marcado, aplicar filtro y seleccionar todas las filas con el mismo nombre
            if (!isFiltered) {
                // Aplicar filtro en la búsqueda de DataTables
                table.search(nombre).draw();
                isFiltered = true;
            }

            // Marcar todas las filas que coincidan con el nombre
            table.rows({ search: 'applied' }).every(function() {
                const rowNode = this.node();
                const rowNombre = $(rowNode).find('.select-row').data('nombre');

                // Si el nombre coincide, marcar la fila
                if (rowNombre === nombre) {
                    $(rowNode).find('.select-row').prop('checked', true);
                }
            });
        } else {
            // Si el checkbox está desmarcado, permitir desmarcar la fila
            if ($('.select-row:checked').length === 0) {
                // Si no hay más checkboxes seleccionados, restablecer el filtro
                table.search('').draw();
                isFiltered = false;
            }
        }
    });

    // Generar el PDF cuando se hace clic en el botón
    $('#generatePDF').on('click', function() {
        let selectedRows = [];

        // Recoger todas las filas seleccionadas
        $('.select-row:checked').each(function() {
            selectedRows.push({
                id: $(this).data('id'),
                nombre: $(this).data('nombre'),
                cotizacion: $(this).data('cotizacion'),
                monto: $(this).data('monto'),
                nota: $(this).data('nota')
            });
        });

        if (selectedRows.length > 0) {
            // Llamar a la función para generar el PDF
            generatePDF(selectedRows);
        } else {
            alert('Por favor selecciona al menos una fila.');
        }
    });

    function generatePDF(rows) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // La URL de la imagen o base64 de la imagen (ejemplo de base64)
    const imgData = 'images/logolateral.png'; // Reemplaza esto con tu imagen base64

    // Añadir la imagen en la parte superior derecha
    const margin = 10; // Margen desde el borde derecho
    const imgWidth = 38; // Ancho de la imagen
    const imgHeight = 15; // Alto de la imagen
    const pageWidth = doc.internal.pageSize.width; // Ancho de la página
    doc.addImage(imgData, 'PNG', pageWidth - imgWidth - margin, margin, imgWidth, imgHeight);

    // Título del PDF
    const nombreUpper = rows.length > 0 ? rows[0].nombre.toUpperCase() : '';
    doc.text(`COMPRAS ${nombreUpper}`, 10, 30); // Ajusta la posición Y si es necesario

    // Definir las columnas de la tabla
    const head = [
        ['#', 'Cotización', 'Notas', 'Monto']
    ];

    // Definir las filas de la tabla con los datos seleccionados
    const body = rows.map((row, index) => [
        index + 1,
        row.cotizacion,
        row.nota,
        `$${parseFloat(row.monto || 0).toFixed(2)}` // Manejar valores vacíos o indefinidos
    ]);

    // Calcular el total de los montos
    const totalMonto = rows.reduce((total, row) => {
        const monto = typeof row.monto === 'string'
            ? parseFloat(row.monto.replace(/[$,]/g, '')) || 0
            : parseFloat(row.monto) || 0;
        
        return total + monto;
    }, 0);

    // Añadir una fila de total al final de la tabla
    body.push(['', '', 'Total', `$${totalMonto.toFixed(2)}`]);

    // Crear la tabla usando autoTable
    doc.autoTable({
        head: head,
        body: body,
        startY: 40 // Iniciar la tabla después del título y la imagen
    });

    // Guardar el PDF
    doc.save(`COMPRAS_${nombreUpper}.pdf`);
}

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

                        fetch('codequotes.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);

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