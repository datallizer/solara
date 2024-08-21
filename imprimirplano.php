<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

// Verificar si se recibe un parámetro "id" a través de la URL
if (isset($_GET['id'])) {
    $plano_id = $_GET['id'];

    // Consulta para obtener el PDF según el ID del plano
    $query = "SELECT medio FROM plano WHERE id = $plano_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $plano = mysqli_fetch_assoc($result);
        $pdf_content = $plano['medio']; // Suponiendo que el contenido del PDF se almacena en la columna 'medio'

        // Mostrar el PDF si se encontró y se pudo obtener el contenido
        if ($pdf_content) {
?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Ver plano | Solara</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
                <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
                <link rel="stylesheet" href="css/styles.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Asegúrate de usar la versión correcta de SweetAlert2 -->
            </head>
            <style>
                .pdf-container {
                    width: 100%;
                    margin: 0 auto;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .pdf-image {
                    max-width: 100%;
                    max-height: 100%;
                }
            </style>
            <body class="sb-nav-fixed">
                <?php include 'mensajes.php'; ?>
                <div id="layoutSidenav">
                    <div id="layoutSidenav_content">
                        <div class="container-fluid">
                            <div class="row m-5">
                                <div class="col-1">
                                    <img src="images/logo.png" alt="">
                                </div>
                                <div class="col-md-12 pdf-container">
                                    <canvas id="pdf-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var pdfData = atob('<?= base64_encode($pdf_content); ?>');
                        var loadingTask = pdfjsLib.getDocument({data: pdfData});
                        loadingTask.promise.then(function(pdf) {
                            pdf.getPage(1).then(function(page) {
                                var scale = 1.5;
                                var viewport = page.getViewport({ scale: scale });
                                var canvas = document.getElementById('pdf-canvas');
                                var context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                var renderContext = {
                                    canvasContext: context,
                                    viewport: viewport
                                };
                                page.render(renderContext);
                            });
                        });

                        setTimeout(function() {
                            window.print(); // Abre el cuadro de impresión después de 3 segundos
                            Swal.fire({
                                title: '¿Has terminado de imprimir?',
                                icon: 'question',
                                showCancelButton: false,
                                confirmButtonText: 'Sí, he terminado',
                                cancelButtonText: 'No, volver a imprimir',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'maquinados.php'; // Redirige a maquinados.php si el usuario confirma
                                } else {
                                    window.print(); // Vuelve a abrir el cuadro de impresión si el usuario elige "No, volver a imprimir"
                                }
                            });
                        }, 1000); // 3000 milisegundos = 3 segundos
                    });
                </script>
                <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js' integrity='sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2' crossorigin='anonymous'></script>
                <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
            </body>
            </html>
<?php
        } else {
            echo "No se encontró el PDF correspondiente al ID del plano.";
        }
    } else {
        echo "No se encontró el plano con el ID proporcionado.";
    }

    mysqli_close($con); // Cerrar la conexión a la base de datos
} else {
    echo "ID no válido.";
}
?>
