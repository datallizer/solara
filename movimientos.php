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
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Dashboard' WHERE `usuarios`.`codigo` = '$codigo'";
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Configuracion SMTP
$host = 'smtp.gmail.com';
$port = 587;
$username = 'solarasystemai@gmail.com';
$password = 'owwd pbtr bpfh brff';
$security = 'tls';

$query = "SELECT * FROM inventario WHERE tipo = 'Consumible'";
$query_run = mysqli_query($con, $query);

while ($registro = mysqli_fetch_assoc($query_run)) {
    // Obtener valores
    $cantidad = $registro['cantidad'];
    $minimo = $registro['minimo'];
    $maximo = $registro['maximo'];

    // Evaluar si la cantidad es menor o igual al mínimo y reorden es igual a "1"
    if ($cantidad <= $minimo && $registro['reorden'] == 1) {

        // Crear instancia PHPMailer
        $mail = new PHPMailer(true);


        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = $security;
        //$mail->SMTPDebug = 2;



        // Configurar correo
        $mail->setFrom('solarasystemai@gmail.com', 'SOLARA AI');
        $mail->addAddress('storage@solara-industries.com');
        $mail->Subject = 'Reorden: Inventario de ' . $registro['id'] . ' ' . $registro['nombre'] . ' bajo en stock';
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        // Cuerpo del mensaje
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body>
            <img style="width:100%;" src="https://datallizer.com/images/solarasuperior.jpg" alt="">
            <h1 style="font-size:25px;margin-top:30px;text-align:left;margin-bottom:30px;"><b>REORDEN</b></h1>
            <p>El producto id ' . $registro['id'] . ' ' . $registro['nombre'] . ' tiene un stock bajo.</p>
            <p><b>Cantidad actual:</b> ' . $cantidad . '</p>
            <p><b>Minimo recomendado:</b> ' . $minimo . '</p>
            <p><b>Maximo recomendado:</b> ' . $maximo . '</p>

            <p style="margin-top:30px;"><b>Detalles</b></p>
            <p><b>Proveedor:</b> ' . $registro['proveedor'] . '</p>
            <p><b>Parte:</b> ' . $registro['parte'] . '</p>
            <p><b>Marca:</b> ' . $registro['marca'] . '</p>
            <p><b>Condición:</b> ' . $registro['condicion'] . '</p>
            <p><b>Costo:</b> $' . $registro['costo'] . '</p>

            <p style="font-size:10px;">Este es un email enviado automaticamente por el sistema de planificación de recursos empresariales SOLARA AI, la información previa a sido generada utilizando datos históricos almacenados en la base de datos de SOLARA, es importante tener en cuenta que las cantidades, materiales u otros detalles presentados en esta propuesta podrían estar desactualizados, descontinuados o contener errores. Le recomendamos verificar la precisión de la información presentada antes de tomar decisiones basadas en estos datos desde los submódulos "Inventario" y "Reorden".</p>
            </body>
            </html>
            ';
        $mail->Body = $body;

        // Enviar correo
        if ($mail->send()) {
            mysqli_query($con, "UPDATE inventario SET reorden = 0 WHERE id = " . $registro['id']);
        } else {
            $_SESSION['message'] = "Error al solicitar reorden";
        }
    } elseif ($cantidad > $minimo && $registro['reorden'] == 0) {
        mysqli_query($con, "UPDATE inventario SET reorden = 1 WHERE id = " . $registro['id']);
    }
}

$sql = "SELECT * FROM asistencia WHERE estatus = 1 AND idcodigo = '$codigo' LIMIT 1";
$result = mysqli_query($con, $sql);

// If a matching record is found, set variables for modal content
if (mysqli_num_rows($result) > 0) {
    $registro = mysqli_fetch_assoc($result);
    $entrada = $registro['entrada'];
    $salida = $registro['salida'];
    $fecha = $registro['fecha'];

    // Convertir la hora de entrada y salida a objetos DateTime
    $entrada_dt = new DateTime($entrada);
    $salida_dt = new DateTime($salida);

    // Calcular la diferencia entre la hora de entrada y salida
    $duracion_jornada = $entrada_dt->diff($salida_dt)->format('%H:%I'); // Formato horas:minutos
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#revision').modal('show');
        });
    </script>
<?php
}
?>

<!-- Modal solicitud salida -->
<div class="modal fade" id="revision" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b>COMPLETAR HORA DE SALIDA</b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeasistencia.php" method="POST" class="row">
                    <input type="hidden" id="id" name="id" value="<?= $registro['id']; ?>">
                    <input type="hidden" id="codigo" name="codigo" value="<?= $registro['idcodigo']; ?>">
                    <div class="col-12">
                        <p class="small">Recibiste una solicitud de revisión sobre tu <b>hora de salida</b>, verifica que los datos sean correctos y si estas de acuerdo aprueba la solicitud.</p>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="text" class="form-control" id="fecha" value="<?= $fecha; ?>" placeholder="Fecha" disabled>
                        <label for="fecha">Fecha <span class="small">(YYYY/MM/DD)</span></label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input type="text" class="form-control" id="entrada" value="<?= $entrada; ?>" placeholder="Entrada" disabled>
                        <label for="entrada">Hora de entrada</label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input style="background-color:#ffdca1;" type="text" class="form-control" id="salida" value="<?= $salida; ?>" placeholder="Salida" disabled>
                        <label for="salida">Hora de salida</label>
                    </div>
                    <div class="col-12">
                        <p>Tu jornada fue de: <b><?= $duracion_jornada; ?></b> <span class="small">hrs</span></p>
                    </div>
                    <div class="modal-footer">
                        <p class="small">Tu jornada total de trabajo se calcula con el número de entradas y salidas que registres en un día, si deseas conocer el total de horas trabajadas para este día puedes consultarlo en <a href="asistenciapersonal.php?id=<?= $registro['idcodigo']; ?>">asistencia</a>.</p>
                        <button type="submit" class="btn btn-danger" name="rechazar">Rechazar</button>
                        <button type="submit" class="btn btn-success" name="aprobar">Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <title>Movimientos | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-md-center justify-content-start mt-5 mb-5">
                    <div class="col-12">
                        <h2 class="mb-3">MOVIMIENTOS DEL SISTEMA</h2>
                    </div>
                    <div class="col-12 p-3 text-center" style="border: 1.5px solid #e7e7e7;">
                        <table id="miTabla" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Usuario</th>
                                    <th scope="col">Detalles</th>
                                    <th scope="col">Hora</th>
                                    <th scope="col">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                    $query = "SELECT h.*, u.nombre, u.apellidop, u.apellidom 
                                    FROM historial h 
                                    INNER JOIN usuarios u ON h.idcodigo = u.codigo
                                    ORDER BY h.id DESC";
                                } else {
                                    $query = "SELECT h.*, u.nombre, u.apellidop, u.apellidom 
                                    FROM historial h 
                                    INNER JOIN usuarios u ON h.idcodigo = u.codigo 
                                    WHERE u.codigo = $codigo
                                    ORDER BY h.id DESC";
                                }

                                $query_run = mysqli_query($con, $query);

                                if (mysqli_num_rows($query_run) > 0) {
                                    foreach ($query_run as $registro) {
                                ?>
                                        <tr>
                                            <td>
                                                <p><?= $registro['id']; ?></p>
                                            </td>
                                            <td><?= $registro['nombre'] . ' ' . $registro['apellidop'] . ' ' . $registro['apellidom']; ?></td>
                                            <td>
                                                <p><?= $registro['detalles']; ?></p>
                                            </td>
                                            <td>
                                                <p><?= $registro['hora']; ?></p>
                                            </td>
                                            <td>
                                                <p><?= $registro['fecha']; ?></p>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5'><p>No se encontró ningún registro</p></td></tr>";
                                }
                                ?>

                            </tbody>
                        </table>
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
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ] // Ordenar la primera columna (índice 0) en orden descendente
            });
        });
    </script>
</body>

</html>