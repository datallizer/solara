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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <title>Dashboard | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-md-center justify-content-start mt-5 mb-5">
                    <div class="col-12">
                        <h2 class="mb-3">MOVIMIENTOS DEL SISTEMA</h2>
                    </div>
                    <div class="col-12 p-3 text-center" style="background-color: #e3e3e3;">
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