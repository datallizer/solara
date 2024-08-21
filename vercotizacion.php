<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Visualizador de quotes' WHERE `usuarios`.`codigo` = '$codigo'";
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

// Verificar si se recibe un parámetro "id" a través de la URL
if (isset($_GET['id'])) {

    $plano_id = $_GET['id'];

    // Consulta para obtener el PDF según el ID del plano
    $query = "SELECT * FROM quotes WHERE id = $plano_id";
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
                <title>Ver cotización | Solara</title>
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
                            <div class="row" style="margin-top: 35px;">
                                <div class="col-md-12" style="width: 100%; height: 100vh;">
                                    <embed src="data:application/pdf;base64,<?= base64_encode($pdf_content); ?>" type="application/pdf" width="100%" height="100%" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
                <!-- Incluir los archivos de PDF.js -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
    echo "ID de cotizacion no válido.";
}
?>