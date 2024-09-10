<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

// Verificar si se recibe un parámetro "id" a través de la URL
if (isset($_GET['id'])) {
    $plano_id = $_GET['id'];

    // Consulta para obtener la ruta del PDF
    $query = "SELECT medio FROM plano WHERE id = $plano_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $plano = mysqli_fetch_assoc($result);
        $pdf_path = $plano['medio']; // Suponiendo que 'medio' tiene la ruta del PDF en el servidor

        // Comprobamos si el archivo realmente existe
        if (file_exists($pdf_path)) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ver plano | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row m-5">
                    <!-- <div class="col-1">
                        <img src="images/logo.png" alt="">
                    </div> -->
                    <div class="col-md-12 pdf-container">
                        <canvas id="pdf-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pdfPath = '<?= $pdf_path ?>';  // Ruta al PDF en el servidor

            var loadingTask = pdfjsLib.getDocument(pdfPath);
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
                window.print();
                Swal.fire({
                    title: '¿Has terminado de imprimir?',
                    icon: 'question',
                    showCancelButton: false,
                    confirmButtonText: 'Sí, he terminado',
                    cancelButtonText: 'No, volver a imprimir',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'maquinados.php';
                    } else {
                        window.print();
                    }
                });
            }, 1000);
        });
    </script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
</body>
</html>
<?php
        } else {
            echo "El archivo PDF no existe en la ruta especificada.";
        }
    } else {
        echo "No se encontró el plano con el ID proporcionado.";
    }

    mysqli_close($con);
} else {
    echo "ID no válido.";
}

?>
