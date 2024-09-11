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
    <title>Dashboard | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-start mt-4 mb-5">
                    <?php
                    $query = "SELECT * FROM proyecto WHERE estatus = 1 ORDER BY prioridad ASC";
                    $query_run = mysqli_query($con, $query);
                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                            // Formato de fechas asumido 'YYYY-MM-DD'
                            $fechainicio = $registro['fechainicio'];
                            $fechafin = $registro['fechafin'];
                            $fechaActual = date('Y-m-d');
                            $diseño_actual = $registro['etapadiseño'];
                            $control_actual = $registro['etapacontrol'];
                            $tcontrol_actual = $registro['etapatcontrol'];
                            $mecanica_actual = $registro['etapamecanica'];

                            // Convertir a objetos DateTime
                            $inicio = new DateTime($fechainicio);
                            $fin = new DateTime($fechafin);
                            $hoy = new DateTime($fechaActual);

                            // Calcular la diferencia de días totales
                            $diasTotales = $inicio->diff($fin)->days;

                            // Evitar división por cero
                            if ($diasTotales == 0) {
                                // Si las fechas de inicio y fin son iguales, asignar el progreso a 100%
                                $progreso = 100;
                                $diasRestantes = 0; // No hay días restantes si son iguales
                            } else {
                                // Calcular los días restantes
                                $diasRestantes = $hoy->diff($fin)->days;
                                if ($hoy > $fin) {
                                    $diasRestantes = 0; // Si ya ha pasado la fecha final, no hay días restantes.
                                }

                                // Calcular el porcentaje de progreso
                                $progreso = 100 - ($diasRestantes / $diasTotales) * 100;
                                if ($progreso < 0) $progreso = 0; // No permitir un valor negativo en el progreso
                                if ($progreso > 100) $progreso = 100; // No permitir más de 100%
                            }

                            // Formatear el progreso para que solo tenga un decimal
                            $progresoFormateado = number_format($progreso, 1);

                            // Calcular la etapa del diseño siempre
                            $etapaDiseno = $registro['etapadiseño']; // Obtener la etapa actual de diseño
                            $etapaControl = $registro['etapacontrol']; // Obtener la etapa actual de control
                            $etapaTControl = $registro['etapatcontrol'];
                            $etapaMecanica = $registro['etapamecanica'];

                            // Definir el nombre de la etapa y el porcentaje de progreso basado en la etapa de diseño
                            switch ($etapaDiseno) {
                                case '1':
                                    $nombreEtapaDiseno = "Diseño";
                                    $progresoetapadiseño = 12.5; // 1/8 del progreso
                                    break;
                                case '2':
                                    $nombreEtapaDiseno = "Revisión interna";
                                    $progresoetapadiseño = 25; // 2/8 del progreso
                                    break;
                                case '3':
                                    $nombreEtapaDiseno = "Revisión con cliente";
                                    $progresoetapadiseño = 37.5; // 3/8 del progreso
                                    break;
                                case '4':
                                    $nombreEtapaDiseno = "Planos";
                                    $progresoetapadiseño = 50; // 4/8 del progreso
                                    break;
                                case '5':
                                    $nombreEtapaDiseno = "Bom";
                                    $progresoetapadiseño = 62.5; // 5/8 del progreso
                                    break;
                                case '6':
                                    $nombreEtapaDiseno = "Manufactura";
                                    $progresoetapadiseño = 75; // 6/8 del progreso
                                    break;
                                case '7':
                                    $nombreEtapaDiseno = "Remediación";
                                    $progresoetapadiseño = 87.5; // 7/8 del progreso
                                    break;
                                case '8':
                                    $nombreEtapaDiseno = "Documentación";
                                    $progresoetapadiseño = 100; // 8/8 del progreso, 100% completo
                                    break;
                                default:
                                    $nombreEtapaDiseno = "Error, contacte a soporte";
                                    $progresoetapadiseño = 0; // Progreso en 0 en caso de error
                                    break;
                            }

                            // Calcular el nombre y el progreso de la etapa de control
                            switch ($etapaControl) {
                                case '1':
                                    $nombreEtapaControl = "Diseño";
                                    $progresoetapacontrol = 11.11; // 1/9 del progreso
                                    break;
                                case '2':
                                    $nombreEtapaControl = "Revisión interna";
                                    $progresoetapacontrol = 22.22; // 2/9 del progreso
                                    break;
                                case '3':
                                    $nombreEtapaControl = "Revisión con cliente";
                                    $progresoetapacontrol = 33.33; // 3/9 del progreso
                                    break;
                                case '4':
                                    $nombreEtapaControl = "Diagramas";
                                    $progresoetapacontrol = 44.44; // 4/9 del progreso
                                    break;
                                case '5':
                                    $nombreEtapaControl = "Bom";
                                    $progresoetapacontrol = 55.55; // 5/9 del progreso
                                    break;
                                case '6':
                                    $nombreEtapaControl = "Manufactura";
                                    $progresoetapacontrol = 66.66; // 6/9 del progreso
                                    break;
                                case '7':
                                    $nombreEtapaControl = "Programación";
                                    $progresoetapacontrol = 77.77; // 7/9 del progreso
                                    break;
                                case '8':
                                    $nombreEtapaControl = "Debugging";
                                    $progresoetapacontrol = 88.88; // 8/9 del progreso
                                    break;
                                case '9':
                                    $nombreEtapaControl = "Documentación";
                                    $progresoetapacontrol = 100; // 9/9 del progreso, 100% completo
                                    break;
                                default:
                                    $nombreEtapaControl = "Error, contacte a soporte";
                                    $progresoetapacontrol = 0; // Progreso en 0 en caso de error
                                    break;
                            }

                            // Calcular el nombre y el progreso de la etapa de control
                            switch ($etapaTControl) {
                                case '1':
                                    $nombreEtapaTControl = "Revisión BOM controles";
                                    $progresoetapatcontrol = 14.28; // 1/9 del progreso
                                    break;
                                case '2':
                                    $nombreEtapaTControl = "Armado de tableros de control";
                                    $progresoetapatcontrol = 28.57; // 2/9 del progreso
                                    break;
                                case '3':
                                    $nombreEtapaTControl = "Pruebas eléctricas y de comunicación";
                                    $progresoetapatcontrol = 42.85; // 3/9 del progreso
                                    break;
                                case '4':
                                    $nombreEtapaTControl = "Remediación";
                                    $progresoetapatcontrol = 57.14; // 4/9 del progreso
                                    break;
                                case '5':
                                    $nombreEtapaTControl = "Ensamble en maquinaria";
                                    $progresoetapatcontrol = 71.42; // 5/9 del progreso
                                    break;
                                case '6':
                                    $nombreEtapaTControl = "Ruteo final";
                                    $progresoetapatcontrol = 85.71; // 6/9 del progreso
                                    break;
                                case '7':
                                    $nombreEtapaTControl = "Etiquetado";
                                    $progresoetapatcontrol = 100; // 7/9 del progreso
                                    break;
                                default:
                                    $nombreEtapaTControl = "Error, contacte a soporte";
                                    $progresoetapatcontrol = 0; // Progreso en 0 en caso de error
                                    break;
                            }

                            // Calcular el nombre y el progreso de la etapa de control
                            switch ($etapaMecanica) {
                                case '1':
                                    $nombreEtapaMecanica = "Revisión BOM mecánico";
                                    $progresoetapamecanica = 16.66; // 1/9 del progreso
                                    break;
                                case '2':
                                    $nombreEtapaMecanica = "Armado de componentes mecánicos";
                                    $progresoetapamecanica = 33.33; // 2/9 del progreso
                                    break;
                                case '3':
                                    $nombreEtapaMecanica = "Pruebas de ensamble";
                                    $progresoetapamecanica = 50; // 3/9 del progreso
                                    break;
                                case '4':
                                    $nombreEtapaMecanica = "Remediación";
                                    $progresoetapamecanica = 66.66; // 4/9 del progreso
                                    break;
                                case '5':
                                    $nombreEtapaMecanica = "Desensamble para acabados";
                                    $progresoetapamecanica = 83.33; // 5/9 del progreso
                                    break;
                                case '6':
                                    $nombreEtapaMecanica = "Armado final";
                                    $progresoetapamecanica = 100; // 6/9 del progreso
                                    break;
                                default:
                                    $nombreEtapaMecanica = "Error, contacte a soporte";
                                    $progresoetapamecanica = 0; // Progreso en 0 en caso de error
                                    break;
                            }
                    ?>
                            <div class="card mt-3 p-3 col-12" style="background-color: #f3f3f3;">
                                <div class="row">
                                    <div class="col-9">
                                        <h4 style="text-transform: uppercase;font-weight:600;">
                                            <button type="button" class="btn btn-dark btn-sm float-end" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Etapas</button>
                                            <div style="max-height: 95vh;" class="modal fade" id="pdfModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <form action="codeproyecto.php" method="post">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="pdfModalLabel"><?= $registro['nombre']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">
                                                                <div class="form-floating col-12 mt-3">
                                                                    <select class="form-select" name="etapadiseño" id="etapadiseño">
                                                                        <option disabled>Seleccione la etapa</option>
                                                                        <option value="1" <?= ($diseño_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                                        <option value="2" <?= ($diseño_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                                        <option value="3" <?= ($diseño_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                                        <option value="4" <?= ($diseño_actual == 4) ? 'selected' : ''; ?>>Planos</option>
                                                                        <option value="5" <?= ($diseño_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                                        <option value="6" <?= ($diseño_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                                        <option value="7" <?= ($diseño_actual == 7) ? 'selected' : ''; ?>>Remediación</option>
                                                                        <option value="8" <?= ($diseño_actual == 8) ? 'selected' : ''; ?>>Documentación</option>
                                                                    </select>
                                                                    <label style="font-size: 15px;text-transform:none;margin-left:0px;" for="etapadiseño">Etapa de diseño</label>
                                                                </div>

                                                                <div class="form-floating col-12 mt-3">
                                                                    <select class="form-select" name="etapacontrol" id="etapacontrol">
                                                                        <option disabled>Seleccione la etapa</option>
                                                                        <option value="1" <?= ($control_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                                        <option value="2" <?= ($control_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                                        <option value="3" <?= ($control_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                                        <option value="4" <?= ($control_actual == 4) ? 'selected' : ''; ?>>Diagramas</option>
                                                                        <option value="5" <?= ($control_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                                        <option value="6" <?= ($control_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                                        <option value="7" <?= ($control_actual == 7) ? 'selected' : ''; ?>>Programación</option>
                                                                        <option value="8" <?= ($control_actual == 8) ? 'selected' : ''; ?>>Debugging</option>
                                                                        <option value="9" <?= ($control_actual == 9) ? 'selected' : ''; ?>>Documentación</option>
                                                                    </select>
                                                                    <label style="font-size: 15px;text-transform:none;margin-left:0px;" for="etapacontrol">Etapa de control</label>
                                                                </div>

                                                                <div class="form-floating col-12 mt-3">
                                                                    <select class="form-select" name="etapatcontrol" id="etapatcontrol">
                                                                        <option disabled>Seleccione la etapa</option>
                                                                        <option value="1" <?= ($tcontrol_actual == 1) ? 'selected' : ''; ?>>Revisión BOM controles</option>
                                                                        <option value="2" <?= ($tcontrol_actual == 2) ? 'selected' : ''; ?>>Armado de tableros de control</option>
                                                                        <option value="3" <?= ($tcontrol_actual == 3) ? 'selected' : ''; ?>>Pruebas electrícas y de comunicación</option>
                                                                        <option value="4" <?= ($tcontrol_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                                        <option value="5" <?= ($tcontrol_actual == 5) ? 'selected' : ''; ?>>Ensamble en maquinaria</option>
                                                                        <option value="6" <?= ($tcontrol_actual == 6) ? 'selected' : ''; ?>>Ruteo final</option>
                                                                        <option value="7" <?= ($tcontrol_actual == 7) ? 'selected' : ''; ?>>Etiquetado</option>
                                                                    </select>
                                                                    <label style="font-size: 15px;text-transform:none;margin-left:0px;" for="etapatcontrol">Etapa ensamble</label>
                                                                </div>

                                                                <div class="form-floating col-12 mt-3">
                                                                    <select class="form-select" name="etapamecanica" id="etapamecanica">
                                                                        <option disabled>Seleccione la etapa</option>
                                                                        <option value="1" <?= ($mecanica_actual == 1) ? 'selected' : ''; ?>>Revisión BOM mecánico</option>
                                                                        <option value="2" <?= ($mecanica_actual == 2) ? 'selected' : ''; ?>>Armado de componentes mecánicos</option>
                                                                        <option value="3" <?= ($mecanica_actual == 3) ? 'selected' : ''; ?>>Pruebas de ensamble</option>
                                                                        <option value="4" <?= ($mecanica_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                                        <option value="5" <?= ($mecanica_actual == 5) ? 'selected' : ''; ?>>Desensamble para acabados</option>
                                                                        <option value="6" <?= ($mecanica_actual == 6) ? 'selected' : ''; ?>>Armado final</option>
                                                                    </select>
                                                                    <label style="font-size: 15px;text-transform:none;margin-left:0px;" for="etapamecanica">Etapa de ensamble mecánica/neumatica</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="etapas" class="btn btn-warning">Actualizar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?= $registro['nombre']; ?>
                                        </h4>
                                        <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Fin del proyecto en:</span> <?= $diasRestantes; ?> Días</p>

                                        <!-- Barra de progreso de días restantes -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-dias" style="width: <?= $progreso; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= $progresoFormateado; ?>%
                                            </div>
                                        </div>

                                        <div>
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Etapa diseño:</span> <?= $nombreEtapaDiseno; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa diseño -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-diseno" style="width: <?= $progresoetapadiseño; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= number_format($progresoetapadiseño, 1); ?>%
                                            </div>
                                        </div>

                                        <div>
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Etapa control:</span> <?= $nombreEtapaControl; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa control -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-control" style="width: <?= $progresoetapacontrol; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= number_format($progresoetapacontrol, 1); ?>%
                                            </div>
                                        </div>

                                        <div>
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Etapa ensamble:</span> <?= $nombreEtapaTControl; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa control -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-control" style="width: <?= $progresoetapatcontrol; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= number_format($progresoetapatcontrol, 1); ?>%
                                            </div>
                                        </div>

                                        <div>
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Etapa mecánica:</span> <?= $nombreEtapaMecanica; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa control -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-control" style="width: <?= $progresoetapamecanica; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= number_format($progresoetapamecanica, 1); ?>%
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-3 text-end">
                                        <p style="margin-top:0px;font-weight:600;">Encargado de proyecto</p>
                                        <div class="row justify-content-end">
                                            <?php
                                            $queryAsignacion = "SELECT encargadoproyecto.*, encargadoproyecto.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo, usuarios.medio
                                                        FROM encargadoproyecto
                                                        JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo 
                                                        WHERE encargadoproyecto.idproyecto = " . $registro['id'];
                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                foreach ($query_run_asignacion as $asignacion) {
                                            ?>

                                                    <div class="col-5 mb-3">
                                                        <img class="mb-1" src="<?= $asignacion['medio']; ?>" alt="">
                                                        <p class="text-center" style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?></p>

                                                    </div>
                                            <?php

                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                    <?php
                        }
                    } else {
                        echo "<p>No se encontró ningún registro</p>";
                    }
                    ?>

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