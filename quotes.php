<?php
session_start();
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
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
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
    <title>Quotes | Solara</title>
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
                                <h4>QUOTES
                                    <?php
                                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 5, 9])) {
                                        echo '<button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Nueva quote
                                </button>';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Solicitante</th>
                                            <th>Rol</th>
                                            <th>Proyecto</th>
                                            <th>PDF</th>
                                            <th>Notas</th>
                                            <th>Estatus</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT quotes.*, proyecto.*, quotes.id AS id_quote
                                            FROM quotes 
                                            JOIN proyecto ON quotes.proyecto = proyecto.id
                                            WHERE estatusq = 1
                                            ORDER BY id_quote ASC";


                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
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
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Cotizacion <?= $registro['cotizacion']; ?></button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="pdfModalLabel">Cotización <?= $registro['cotizacion']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <iframe src="data:application/pdf;base64,<?= base64_encode($registro['medio']); ?>" width="100%" height="600px"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= $registro['notas']; ?></td>
                                                    <td><?php
                                                        if ($registro['estatusq'] === '0') {
                                                            echo "Aprobado";
                                                        } else if ($registro['estatusq'] === '1') {
                                                            echo "Pendiente";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?></td>
                                                    <td>
                                                        <form action="codequotes.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="<?= $registro['id_quote']; ?>" class="btn btn-danger btn-sm m-1 float-end deletebtn"><i class="bi bi-trash-fill"></i></button>
                                                            <?php
                                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])){
                                                                echo '<button type="submit" name="aprobar" value="' . $registro['id_quote'] . '" class="btn btn-success btn-sm m-1 float-end"><i class="bi bi-check-lg"> Aprobar</i></button>';
                                                            }
                                                            ?>                                                          
                                                        </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'><p>No se encontró ningún registro</p></td></tr>";
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
    <!-- Modal Mecanica-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">NUEVO QUOTE</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codequotes.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="proyecto" id="proyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        $opcion = $proyecto['nombre'];
                                        $idProyecto = $proyecto['id'];
                                        echo "<option value='$idProyecto'>$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="proyecto">Proyecto asociado</label>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="solicitante" id="solicitante" placeholder="Solicitante" autocomplete="off" value="<?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidop'] . ' ' . $_SESSION['apellidom']; ?>" readonly>
                            <label for="solicitante">Solicitante</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="hidden" class="form-control" name="rol" id="rol" placeholder="Rol" value="<?php echo $_SESSION['rol']; ?>" readonly>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="text" class="form-control" name="cotizacion" id="cotizacion" placeholder="Nombre cotización" autocomplete="" required>
                            <label for="cotizacion">Nombre cotización</label>
                        </div>

                        <div class="mt-3">
                            <label for="medio" class="form-label">Cotización (PDF)</label>
                            <input class="form-control" type="file" id="medio" name="medio" max="100000">
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <textarea type="text" class="form-control" name="notas" id="notas" placeholder="Notas" autocomplete="" style="min-height: 150px;"></textarea>
                            <label for="notas">Notas</label>
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
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTabla, #miTablaDos').DataTable({
                "order": [
                    [0, "asc"]
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

        const deleteButtons = document.querySelectorAll('.deletebtn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                const id_quote = e.target.value; // Obtener el valor del botón delete

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
                        formData.append('delete', id_quote);

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