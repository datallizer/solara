<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
// Modal actualizar proyecto al presionar link de notificacioes
$modalId = '';
$selectValue = '';
$message = '';

if (isset($_GET['proyecto_id'])) {
    $modalId = htmlspecialchars($_GET['proyecto_id']);
    $selectValue = '13'; // Valor para el select de proyecto
    $message = 'Actualiza la etapa a "Construcción del equipo"';
} elseif (isset($_GET['internas_id'])) {
    $modalId = htmlspecialchars($_GET['internas_id']);
    $selectValue = '14'; // Valor para el select de internas
    $message = 'Actualiza la etapa a "Pruebas internas iniciales"';
}

if ($modalId) {
?>
    <script>
        // Espera a que el DOM esté completamente cargado
        document.addEventListener("DOMContentLoaded", function() {
            // Abre el modal correspondiente al proyecto usando el ID
            var modal = new bootstrap.Modal(document.getElementById('pdfModal<?php echo $modalId; ?>'));
            modal.show();

            // Selecciona automáticamente la opción en el select con id="etapa"
            var selectEtapa = document.getElementById("etapa<?php echo $modalId; ?>");
            if (selectEtapa) {
                selectEtapa.value = "<?php echo $selectValue; ?>";

                // Obtener la opción correspondiente y aplicarle el fondo amarillo
                var optionToHighlight = selectEtapa.querySelector('option[value="<?php echo $selectValue; ?>"]');
                if (optionToHighlight) {
                    optionToHighlight.style.backgroundColor = 'yellow';
                }
            }
        });
    </script>
<?php
    $_SESSION['message'] = $message; // Establece el mensaje de sesión
}

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
        $queryemail = "SELECT * FROM usuarios WHERE codigo = '$codigo' AND rol IN (5, 9, 13,1,2) AND estatus = 1";
        $result = $con->query($queryemail);

        // Verifica si hay resultados
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verifica si el email está vacío
            if (empty($row['email'])) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Registra tu correo institucional',
                        icon: 'warning',
                        html: `<form class='row text-center' id='emailForm' method='POST' action='codeusuarios.php'>
                                    <div class='form-floating col-12'>
                                        <input id='email' class='form-control' style='width:100%;min-height:60px;' type='email' name='email' placeholder='Ingresa tu email' required>
                                        <label for='email'>Correo</label>
                                    </div>
                                    <div class='col-12 mt-3'>
                                    <input type='hidden' name='emailsave'>
                                        <button style='min-height:60px;' class='btn btn-primary w-100' type='submit'>Guardar</button>
                                    </div>
                            </form>`,
                        showCloseButton: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            document.getElementById('emailForm').addEventListener('submit', function(event) {
                                event.preventDefault(); // Evita el envío normal
                                // Aquí puedes hacer cualquier validación adicional si es necesario
                                this.submit(); // Envía el formulario
                            });
                        }
                    });
                });
              </script>";
            }
        } else {
            echo "No se encontró el usuario.";
        }
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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <title>Dashboard | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <?php include 'mensajes.php'; ?>
    <?php include 'modales.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-start mt-4 mb-5">
                    <?php
                    $query = "SELECT * FROM proyecto WHERE estatus = 1 AND nombre <> 'Maquinados' AND nombre <> 'Cotizaciones y Pruebas' ORDER BY prioridad ASC";
                    $query_run = mysqli_query($con, $query);
                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                            $fechainicio = $registro['fechainicio'];
                            $fechafin = $registro['fechafin'];
                            $fechaActual = date('Y-m-d');
                            $etapa = $registro['etapa'];
                            $inicio = new DateTime($fechainicio);
                            $fin = new DateTime($fechafin);
                            $hoy = new DateTime($fechaActual);
                            $diasTotales = $inicio->diff($fin)->days;
                            if ($diasTotales == 0) {
                                $progreso = 100;
                                $diasRestantes = 0;
                            } else {
                                $diasRestantes = $hoy->diff($fin)->days;
                                if ($hoy > $fin) {
                                    $diasRestantes = 0;
                                }

                                $progreso = 100 - ($diasRestantes / $diasTotales) * 100;
                                if ($progreso < 0) $progreso = 0;
                                if ($progreso > 100) $progreso = 100;
                            }
                            $progresoFormateado = number_format($progreso, 1);
                            $etapaactual = $registro['etapa'];

                            switch ($etapa) {
                                case '6':
                                    $nombreEtapa = "<b>Recepción de PO</b> <span class='small'>(Etapa 1 de 16)</span>";
                                    $progresoEtapa = 6.25;
                                    break;
                                case '7':
                                    $nombreEtapa = "<b>Kick off meeting</b> <span class='small'>(Etapa 2 de 16)</span>";
                                    $progresoEtapa = 12.5;
                                    break;
                                case '8':
                                    $nombreEtapa = "<b>Visita formal de levantamiento</b> <span class='small'>(Etapa 3 de 16)</span>";
                                    $progresoEtapa = 18.75;
                                    break;
                                case '9':
                                    $nombreEtapa = "<b>Prediseño (mecánico y eléctrico)</b> <span class='small'>(Etapa 4 de 16)</span>";
                                    $progresoEtapa = 25;
                                    break;
                                case '10':
                                    $nombreEtapa = "<b>Revisión de diseño/aprobación de cliente</b> <span class='small'>(Etapa 5 de 16)</span>";
                                    $progresoEtapa = 31.25;
                                    break;
                                case '11':
                                    $nombreEtapa = "<b>Actualizació de BOM</b> <span class='small'>(Etapa 6 de 16)</span>";
                                    $progresoEtapa = 37.5;
                                    break;
                                case '12':
                                    $nombreEtapa = "<b>Colocación de PO's</b> <span class='small'>(Etapa 7 de 16)</span>";
                                    $progresoEtapa = 43.75;
                                    break;
                                case '13':
                                    $nombreEtapa = "<b>Construcción del equipo</b> <span class='small'>(Etapa 8 de 16)</span>";
                                    $progresoEtapa = 50;
                                    $etapaactual = 13;
                                    break;
                                case '14':
                                    $nombreEtapa = "<b>Pruebas internas iniciales</b> <span class='small'>(Etapa 9 de 16)</span>";
                                    $progresoEtapa = 56.25;
                                    break;
                                case '15':
                                    $nombreEtapa = "<b>Debugging interno y pruebas secundarias</b> <span class='small'>(Etapa 10 de 16)</span>";
                                    $progresoEtapa = 62.5;
                                    break;
                                case '16':
                                    $nombreEtapa = "<b>Buf off interno</b> <span class='small'>(Etapa 11 de 16)</span>";
                                    $progresoEtapa = 68.75;
                                    break;
                                case '17':
                                    $nombreEtapa = "<b>Buy off con cliente</b> <span class='small'>(Etapa 12 de 16)</span>";
                                    $progresoEtapa = 75;
                                    break;
                                case '18':
                                    $nombreEtapa = "<b>Empaque y envío a instalaciones de cliente</b> <span class='small'>(Etapa 13 de 16)</span>";
                                    $progresoEtapa = 81.25;
                                    break;
                                case '19':
                                    $nombreEtapa = "<b>Instalción con cliente</b> <span class='small'>(Etapa 14 de 16)</span>";
                                    $progresoEtapa = 87.5;
                                    break;
                                case '20':
                                    $nombreEtapa = "<b>Arranque y validación de equipo (buy off)</b> <span class='small'>(Etapa 15 de 16)</span>";
                                    $progresoEtapa = 93.75;
                                    break;
                                case '21':
                                    $nombreEtapa = "<b>Entrenamiento</b> <span class='small'>(Etapa 16 de 16)</span>";
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
                                            <?php
                                            // Suponiendo que el usuario autenticado tiene su código en $_SESSION['codigo']
                                            $codigoUsuario = $_SESSION['codigo'];

                                            // Consulta para obtener todos los codigooperador del proyecto
                                            $queryAsignacion = "SELECT usuarios.codigo AS codigooperador 
                    FROM encargadoproyecto
                    JOIN usuarios ON encargadoproyecto.codigooperador = usuarios.codigo 
                    WHERE encargadoproyecto.idproyecto = " . $registro['id'];

                                            $resultAsignacion = mysqli_query($con, $queryAsignacion);

                                            // Crear un array para almacenar los códigos de operadores del proyecto
                                            $codigoOperadores = [];

                                            while ($asignacion = mysqli_fetch_assoc($resultAsignacion)) {
                                                $codigoOperadores[] = $asignacion['codigooperador'];
                                            }

                                            // Verificar si el usuario autenticado está en la lista de operadores
                                            if (in_array($codigoUsuario, $codigoOperadores) || $_SESSION['rol'] == 1) :
                                            ?>
                                                <button type="button" class="btn btn-dark btn-sm float-end" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $registro['id']; ?>">Etapas</button>
                                            <?php endif; ?>


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
                                                                <div class="form-floating col-12">
                                                                    <select class="form-select" name="etapa" id="etapa<?= $registro['id']; ?>">
                                                                        <option disabled>Seleccione una etapa</option>
                                                                        <option disabled>------- Ejecución -------</option>
                                                                        <option value="7" <?= ($etapa == 6) ? 'selected' : ''; ?>>Recepción de PO</option>
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
                                                                        <option value="17" <?= ($etapa == 17) ? 'selected' : ''; ?>>Buf off con cliente</option>
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
                                                                <button type="submit" name="etapas" class="btn btn-warning mb-3">Actualizar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?= $registro['nombre']; ?>
                                        </h4>
                                        <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 700;"><i class="bi bi-calendar-week" style='color:#858585;'></i> Fin del proyecto en:</span> <?= $diasRestantes; ?> Días</p>

                                        <!-- Barra de progreso de días restantes -->
                                        <div class="mt-1" style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-dias" style="width: <?= $progreso; ?>%; background-color: #4d94eb; padding: 5px;"></div>
                                        </div>

                                        <div class="mb-1">
                                            <p style="margin-bottom:0px;margin-top:15px;"><span style="font-weight: 600;"><i class='bi bi-check-circle' style='color:#858585;'></i> </span> <?= $nombreEtapa; ?></p>
                                        </div>

                                        <!-- Barra de progreso de etapa diseño -->
                                        <div style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                                            <div class="progress-bar-etapa-diseno" style="width: <?= $progresoEtapa; ?>%; background-color: #4d94eb; padding: 5px;"></div>
                                        </div>

                                        <?php
                                        if ($etapaactual == 13) {
                                            $idProyectoR = $registro['id'];

                                            // Consulta para seleccionar todos los planos con el idproyecto dado y con estatusplano igual a 0
                                            $query = "SELECT 
                COUNT(CASE WHEN estatusplano = 0 THEN 1 END) as totalPlanosEstatus0,
                SUM(CASE WHEN estatusplano = 0 THEN piezas ELSE 0 END) as sumaPiezasEstatus0,
                COUNT(CASE WHEN estatusplano IN (1, 2, 3) THEN 1 END) as totalPlanosEstatus123,
                SUM(CASE WHEN estatusplano IN (1, 2, 3) THEN piezas ELSE 0 END) as sumaPiezasEstatus123
            FROM (
                SELECT estatusplano, piezas 
                FROM archivoplano 
                WHERE idproyecto = $idProyectoR AND estatusplano = 0
                UNION ALL
                SELECT estatusplano, piezas 
                FROM plano 
                WHERE idproyecto = $idProyectoR AND estatusplano IN (1, 2, 3)
            ) AS planos";


                                            $result = mysqli_query($con, $query);

                                            if ($result) {
                                                $data = mysqli_fetch_assoc($result);

                                                $totalPlanosEstatus0 = $data['totalPlanosEstatus0'];
                                                $sumaPiezasEstatus0 = $data['sumaPiezasEstatus0'];
                                                $totalPlanosEstatus123 = $data['totalPlanosEstatus123'];
                                                $sumaPiezasEstatus123 = $data['sumaPiezasEstatus123'];

                                                $totalplanos0123 = $totalPlanosEstatus0 + $totalPlanosEstatus123;
                                                $sumaPiezasEstatus0123 = $sumaPiezasEstatus123 + $sumaPiezasEstatus0;
                                            } else {
                                                echo "Error en la consulta: " . mysqli_error($con);
                                            }

                                            // Consulta para seleccionar todos los ensambles con el idproyecto dado y con estatusplano igual a 0
                                            $queryEnsambles = "SELECT 
                                             COUNT(CASE WHEN estatusplano = 0 THEN 1 END) as totalEnsamblesEstatus0,
                                             SUM(CASE WHEN estatusplano = 0 THEN piezas ELSE 0 END) as sumaPiezasEstatusEnsambles0,
                                             COUNT(CASE WHEN estatusplano IN (0, 1, 2, 3) THEN 1 END) as totalEnsamblesEstatus123,
                                             SUM(CASE WHEN estatusplano IN (0, 1, 2, 3) THEN piezas ELSE 0 END) as sumaPiezasEstatusEnsambles123
                                         FROM diagrama 
                                         WHERE idproyecto = $idProyectoR";

                                            // Ejecutar la consulta
                                            $resultEnsambles = mysqli_query($con, $queryEnsambles);

                                            // Verificar que la consulta no haya fallado
                                            if ($resultEnsambles) {
                                                $data = mysqli_fetch_assoc($resultEnsambles);

                                                $totalEnsamblesEstatus0 = $data['totalEnsamblesEstatus0'];
                                                $sumaPiezasEstatusEnsambles0 = $data['sumaPiezasEstatusEnsambles0'];
                                                $totalEnsamblesEstatus123 = $data['totalEnsamblesEstatus123'];
                                                $sumaPiezasEstatusEnsambles123 = $data['sumaPiezasEstatusEnsambles123'];
                                            } else {
                                                echo "Error en la consulta: " . mysqli_error($con);
                                            }
                                        ?>
                                            <div style="margin-left: 50px;font-size:12px;" class="mt-3">
                                                <p class="small"><b>Subtareas:</b></p>
                                                <?php
                                                // Comparar las variables y mostrar el contenido correspondiente
                                                if ($totalPlanosEstatus0 == $totalplanos0123) {
                                                    echo '<p><i style="color:#15bf45;" class="bi bi-check-circle-fill"></i> Planos/actividades finalizados (Total: ' . $totalPlanosEstatus0 . ' de ' . $totalplanos0123 . ')</p>';
                                                    echo '<p><i style="color:#15bf45;" class="bi bi-check-circle-fill"></i> Se maquinaron todas las piezas (Total: ' . $sumaPiezasEstatus0 . ' de ' . $sumaPiezasEstatus0123 . ')</p>';
                                                } else {
                                                    echo '<p><i class="bi bi-check-circle"></i> Planos/actividades finalizados (Total: ' . $totalPlanosEstatus0 . ' de ' . $totalplanos0123 . ')</p>';
                                                    echo '<p><i class="bi bi-check-circle"></i> Se maquinaron todas las piezas (Total: ' . $sumaPiezasEstatus0 . ' de ' . $sumaPiezasEstatus0123 . ')</p>';
                                                }

                                                if ($totalEnsamblesEstatus0 == $totalEnsamblesEstatus123) {
                                                    echo '<p><i style="color:#15bf45;" class="bi bi-check-circle-fill"></i> Diagramas/actividades finalizadas (Total: ' . $totalEnsamblesEstatus0 . ' de ' . $totalEnsamblesEstatus123 . ')</p>';
                                                    echo '<p><i style="color:#15bf45;" class="bi bi-check-circle-fill"></i> Se ensamblaron todas las piezas (Total: ' . $sumaPiezasEstatusEnsambles0 . ' de ' . $sumaPiezasEstatusEnsambles123 . ')</p>';
                                                } else {
                                                    echo '<p><i class="bi bi-check-circle"></i> Diagramas/actividades finalizadas (Total: ' . $totalEnsamblesEstatus0 . ' de ' . $totalEnsamblesEstatus123 . ')</p>';
                                                    echo '<p><i class="bi bi-check-circle"></i> Se ensamblaron todas las piezas (Total: ' . $sumaPiezasEstatusEnsambles0 . ' de ' . $sumaPiezasEstatusEnsambles123 . ')</p>';
                                                }
                                                ?>
                                            </div>
                                        <?php
                                        }
                                        ?>

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
                                                                    echo "Ing. Control";
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
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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