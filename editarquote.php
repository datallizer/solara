<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Editando quote' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Editar reorden | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-5">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                        <?php
                                
                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM quotes WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);

                                ?>
                            <div class="card-header">
                                <h4>EDITAR QUOTE
                                <a href="quotes.php" class="btn btn-danger btn-sm float-end m-1">Regresar</a>
                                <button type="button" class="btn btn-primary btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Ver cotizacion <?= $registro['cotizacion']; ?></button>
                                                        <div class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title text-dark" id="pdfModalLabel">Cotización <?= $registro['cotizacion']; ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <iframe src="<?= $registro['medio']; ?>" width="100%" height="600px"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                    
                                </h4>
                            </div>
                            <div class="card-body">

                                
                                
                                        <form action="codequotes.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <textarea type="text" style="min-height: 380px;" class="form-control" name="notas" id="notas"><?= $registro['notas']; ?></textarea>
                                                    <label for="notas">Notas</label>
                                                </div>
                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-warning">
                                                        Actualizar
                                                    </button>
                                                </div>


                                            </div>
                            </div>

                            </form>
                    <?php
                                    } else {
                                        echo "<h4>No such ID found</h4>";
                                    }
                                }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>