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
                    $query = "SELECT * FROM proyecto WHERE estatus = 1 AND nombre <> 'Maquinados' AND nombre <> 'Cotizaciones y Pruebas' ORDER BY prioridad ASC";
                    $query_run = mysqli_query($con, $query);
                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                            // Formato de fechas asumido 'YYYY-MM-DD'
                            $fechainicio = $registro['fechainicio'];
                            $fechafin = $registro['fechafin'];
                            $fechaActual = date('Y-m-d');
                            $etapa = $registro['etapa'];

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
                            $etapa = $registro['etapa']; // Obtener la etapa actual de diseño


                            // Definir el nombre de la etapa y el porcentaje de progreso basado en la etapa de diseño
                            switch ($etapa) {
                                case '6':
                                    $nombreEtapa = "Recepción de PO";
                                    $progresoEtapa = 6.25; 
                                    break;
                                case '7':
                                    $nombreEtapa = "Kick off meeting";
                                    $progresoEtapa = 12.5; 
                                    break;
                                case '8':
                                    $nombreEtapa = "Visita formal de levantamiento";
                                    $progresoEtapa = 18.75;
                                    break;
                                case '9':
                                    $nombreEtapa = "Prediseño (mecánico y eléctrico)";
                                    $progresoEtapa = 25; 
                                    break;
                                case '10':
                                    $nombreEtapa = "Revisión de diseño/aprobación de cliente";
                                    $progresoEtapa = 31.25; 
                                    break;
                                case '11':
                                    $nombreEtapa = "Actualizació de BOM";
                                    $progresoEtapa = 37.5; 
                                    break;
                                case '12':
                                    $nombreEtapa = "Colocación de PO's";
                                    $progresoEtapa = 43.75; 
                                    break;
                                case '13':
                                    $nombreEtapa = "Construcción del equipo";
                                    $progresoEtapa = 50;
                                    break;
                                case '14':
                                    $nombreEtapa = "Pruebas internas iniciales";
                                    $progresoEtapa = 56.25; 
                                    break;
                                case '15':
                                    $nombreEtapa = "Debugging interno y pruebas secundarias";
                                    $progresoEtapa = 62.5; 
                                    break;
                                case '16':
                                    $nombreEtapa = "Buf off interno";
                                    $progresoEtapa = 68.75; 
                                    break;
                                case '17':
                                    $nombreEtapa = "Buy off con cliente";
                                    $progresoEtapa = 75;
                                    break;
                                case '18':
                                    $nombreEtapa = "Empaque y envío a instalaciones de cliente";
                                    $progresoEtapa = 81.25; 
                                    break;
                                case '19':
                                    $nombreEtapa = "Instalción con cliente";
                                    $progresoEtapa = 87.5;
                                    break;
                                case '20':
                                    $nombreEtapa = "Arranque y validación de equipo (buy off)";
                                    $progresoEtapa = 93.75;
                                    break;
                                case '21':
                                    $nombreEtapa = "Entrenamiento";
                                    $progresoEtapa = 100; 
                                    break;
                                default:
                                    $nombreEtapa = "Error, contacte a soporte";
                                    $progresoEtapa = 0;
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
                                                                    <select class="form-select" name="etapa" id="etapa">
                                                                        <option disabled>Seleccione una etapa</option>
                                                                        <option disabled>------- Ejecución -------</option>
                                                                        <option value="6" <?= ($etapa == 6) ? 'selected' : ''; ?>>Recepción de PO</option>
                                                                        <option value="7" <?= ($etapa == 7) ? 'selected' : ''; ?>>Kick off meeting</option>
                                                                        <option value="8" <?= ($etapa == 8) ? 'selected' : ''; ?>>Visita formal de levantamiento</option>
                                                                        <option value="9" <?= ($etapa == 9) ? 'selected' : ''; ?>>Prediseño (mecánico y eléctrico)</option>
                                                                        <option value="10" <?= ($etapa == 10) ? 'selected' : ''; ?>>Revisión de diseño/aprobación de cliente</option>
                                                                        <option value="11" <?= ($etapa == 11) ? 'selected' : ''; ?>>Actualización de BOM</option>
                                                                        <option value="12" <?= ($etapa == 12) ? 'selected' : ''; ?>>Colocación de PO's</option>
                                                                        <option value="13" <?= ($etapa == 13) ? 'selected' : ''; ?>>Construcción del equipo</option>
                                                                        <option value="14" <?= ($etapa == 14) ? 'selected' : ''; ?>>Pruebas internas iniciales</option>
                                                                        <option value="15" <?= ($etapa == 15) ? 'selected' : ''; ?>>Debugging interno y pruebas secundarias</option>
                                                                        <option value="16" <?= ($etapa == 16) ? 'selected' : ''; ?>>Buf off interno</option>
                                                                        <option value="17" <?= ($etapa == 17) ? 'selected' : ''; ?>>Buy off con cliente</option>
                                                                        <option value="18" <?= ($etapa == 18) ? 'selected' : ''; ?>>Empaque y envío a instalaciones de cliente</option>
                                                                        <option disabled>------- Validación -------</option>
                                                                        <option value="19" <?= ($etapa == 19) ? 'selected' : ''; ?>>Instalación con cliente</option>
                                                                        <option value="20" <?= ($etapa == 20) ? 'selected' : ''; ?>>Arranque y validación de equipo (buy off)</option>
                                                                        <option value="21" <?= ($etapa == 21) ? 'selected' : ''; ?>>Entrenamiento</option>
                                                                    </select>
                                                                    <label style="font-size: 15px;text-transform:none;padding-left:0px;" for="etapa">Etapa de proyecto:</label>
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
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;">Etapa:</span> <?= $nombreEtapa; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa diseño -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-diseno" style="width: <?= $progresoEtapa; ?>%; background-color: #4d94eb; padding: 5px;">
                                                <?= number_format($progresoEtapa, 1); ?>%
                                            </div>
                                        </div>


                                    </div>
                                    <div class="col-3 text-end">
                                        <p style="margin-top:0px;font-weight:600;">Encargado de proyecto</p>
                                        <div class="row justify-content-end">
                                            <?php
                                            $queryAsignacion = "SELECT encargadoproyecto.*, encargadoproyecto.id AS id_encargado, usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.codigo, usuarios.medio, usuarios.rol
                                                        FROM encargadoproyecto
                                                        JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo 
                                                        WHERE encargadoproyecto.idproyecto = " . $registro['id'];
                                            $query_run_asignacion = mysqli_query($con, $queryAsignacion);
                                            if (mysqli_num_rows($query_run_asignacion) > 0) {
                                                foreach ($query_run_asignacion as $asignacion) {
                                            ?>

                                                    <div class="col-5 mb-3">
                                                        <img class="mb-1" src="<?= $asignacion['medio']; ?>" alt="">
                                                        <p class="text-start" style="margin: 0;"><?= $asignacion['nombre']; ?> <?= $asignacion['apellidop']; ?> <?= $asignacion['apellidom']; ?><br><span style="color: #878787;" class="small">
                                                                <?php
                                                                $rol = $asignacion['rol'];
                                                                if ($rol == 5) {
                                                                    echo "Ing. Diseño";
                                                                } elseif ($rol == 9) {
                                                                    echo "Ing. Controles";
                                                                } elseif ($rol == 13) {
                                                                    echo "Ing. Lazer";
                                                                } else {
                                                                    echo "Rol desconocido"; // O cualquier otro mensaje por defecto
                                                                }
                                                                ?>
                                                            </span></p>

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