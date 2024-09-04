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

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Estadística técnico mecánico' WHERE `usuarios`.`codigo` = '$codigo'";
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Estadística Operador | Solara</title>
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
            <div class="container mt-5">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12">
                        <h3 class="p-2 bg-dark text-light align-items-top" style="text-transform: uppercase;border-radius:5px;">
                            <?php
                            if (isset($_GET['id'])) {
                                $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                $query = "SELECT * FROM usuarios WHERE id='$registro_id' AND estatus = 1";
                                $query_run = mysqli_query($con, $query);

                                if (mysqli_num_rows($query_run) > 0) {
                                    $registro = mysqli_fetch_array($query_run);
                                    $nombre = $registro['nombre'];
                                    $apellidop = $registro['apellidop'];
                                    $apellidom = $registro['apellidom'];
                                    $codigouser = $registro['codigo'];
                                    $_SESSION['userid'] = $registro['codigo'];
                            ?>
                                    <div class="row">
                                        <div class="col-1"><img style="width: 100%;border-radius:5px;height:100px;object-fit: cover;" src="data:image/jpeg;base64,<?php echo base64_encode($registro['medio']); ?>" alt="Foto perfil"></div>
                                        <div class="col-11">
                                            <a href="estadisticas.php#operadores" class="btn btn-primary btn-sm float-end">Regresar</a>
                                            <b>Estadística</b><br>
                                            Maquinados de <b><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></b><br>
                                            <p style="font-size: 15px;text-transform:capitalize"><?php
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
                                                                                                    } else if ($registro['rol'] === '10') {
                                                                                                        echo "Recursos humanos";
                                                                                                    } else {
                                                                                                        echo "Error, contacte a soporte";
                                                                                                    }
                                                                                                    ?></p>

                                        </div>
                                    </div>


                            <?php
                                }
                            }
                            ?>


                        </h3>
                    </div>
                    <?php
                    $planosSeleccionados = isset($_POST['planos']) ? $_POST['planos'] : [];

                    $query = "SELECT historialoperadores.*, plano.nombreplano 
                                                  FROM historialoperadores 
                                                  INNER JOIN plano ON historialoperadores.idplano = plano.id 
                                                  WHERE historialoperadores.idcodigo = '$codigouser' 
                                                  AND plano.estatusplano = 0
                                                  AND motivoactividad <> 'Inicio' 
                                                  AND motivoactividad <> 'Fin de jornada laboral' 
                                                  AND motivoactividad <> 'Atención a otra prioridad'
                                                  AND motivoactividad <> 'Lunch'
                                                  AND plano.estatusplano = 0";
                    $query_run = mysqli_query($con, $query);

                    // Array para acumular el tiempo total por motivoactividad
                    $tiemposPorMotivo = [];

                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                            // Convertir las fechas y horas en objetos DateTime
                            $fechaInicio = new DateTime($registro['fecha'] . ' ' . $registro['hora']);
                            $fechaFin = new DateTime($registro['fechareinicio'] . ' ' . $registro['horareinicio']);

                            // Calcular la diferencia
                            $intervalo = $fechaInicio->diff($fechaFin);

                            // Calcular el tiempo total en minutos
                            $totalMinutos = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

                            // Acumular el tiempo por motivo
                            $motivo = $registro['motivoactividad'];
                            if (isset($tiemposPorMotivo[$motivo])) {
                                $tiemposPorMotivo[$motivo] += $totalMinutos;
                            } else {
                                $tiemposPorMotivo[$motivo] = $totalMinutos;
                            }

                            // Formatear el resultado en días, horas y minutos
                            $totalTiempo = $intervalo->format('%d días, %h horas, %i minutos');
                        }
                    }
                    ?>




                    <div class="col-4">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-center">Maquinados finalizados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    mysqli_query($con, "SET lc_time_names = 'es_ES';");

                                    // Obtener el año actual
                                    $year = date('Y');

                                    // Consulta para agrupar por mes y contar maquinados finalizados por idplano en el año actual
                                    $query = "
                                        SELECT 
                                            DATE_FORMAT(fecha, '%m') AS mes_num, 
                                            DATE_FORMAT(fecha, '%M') AS mes,
                                            COUNT(DISTINCT idplano) AS finalizados
                                        FROM 
                                            historialoperadores 
                                        WHERE 
                                            idcodigo = $codigouser AND 
                                            YEAR(fecha) = '$year'
                                        GROUP BY 
                                            MONTH(fecha)
                                        ORDER BY 
                                            MONTH(fecha) ASC
                                    ";

                                    $query_run = mysqli_query($con, $query);

                                    $meses = [];
                                    $finalizados = [];

                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $registro) {
                                            $meses[] = $registro['mes']; // Nombres de los meses
                                            $finalizados[] = $registro['finalizados']; // Cantidad de maquinados finalizados
                                    ?>
                                            <tr>
                                                <td style="text-transform: capitalize;"><?php echo $registro['mes']; ?></td>
                                                <td class="text-center"><?php echo $registro['finalizados']; ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<td colspan='2'><p>No se encontró ningún registro</p></td>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-8 mt-5">
                        <canvas id="miGrafica"></canvas>
                    </div>

                    <div class="col-8 mt-3">
                        <canvas id="graficaMotivos"></canvas>
                    </div>

                    <div class="col-4 text-center">
                        <h4>% De tiempo invertido en paro de actividades</h4>
                    </div>
                </div>
                <div class="row bg-dark p-5 mb-3 mt-3">
                    <?php

                    // Calcular piezas totales
                    $query_piezas_totales = "SELECT SUM(piezas) AS piezas_totales FROM plano p 
        INNER JOIN asignacionplano a ON p.id = a.idplano 
        WHERE a.codigooperador = '$codigouser'AND p.estatusplano = 1";
                    $query_run_piezas_totales = mysqli_query($con, $query_piezas_totales);
                    $piezas_totales = 0;

                    if (mysqli_num_rows($query_run_piezas_totales) > 0) {
                        $registro_piezas_totales = mysqli_fetch_assoc($query_run_piezas_totales);
                        $piezas_totales = $registro_piezas_totales['piezas_totales'];
                    }

                    // Calcular piezas terminadas
                    $query_piezas_terminadas = "SELECT SUM(piezas) AS piezas_terminadas FROM plano p 
                                                INNER JOIN asignacionplano a ON p.id = a.idplano 
                                                WHERE a.codigooperador = '$codigouser' AND p.estatusplano = 2";
                    $query_run_piezas_terminadas = mysqli_query($con, $query_piezas_terminadas);
                    $piezas_terminadas = 0;

                    if (mysqli_num_rows($query_run_piezas_terminadas) > 0) {
                        $registro_piezas_terminadas = mysqli_fetch_assoc($query_run_piezas_terminadas);
                        $piezas_terminadas = $registro_piezas_terminadas['piezas_terminadas'];
                    }

                    ?>
                    <div class="col-6 text-center">
                        <p style="color: #fff;font-size:18px;">Piezas asignadas: <br><?php echo $piezas_totales; ?></p>
                    </div>

                    <div class="col-6 text-center">
                        <p style="color: #fff;font-size:18px;">Piezas pausadas: <br><?php echo $piezas_terminadas; ?></p>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('graficaMotivos').getContext('2d');
                const data = {
                    labels: <?php echo json_encode(array_keys($tiemposPorMotivo)); ?>,
                    datasets: [{
                        label: 'Tiempo total por motivo (minutos)',
                        data: <?php echo json_encode(array_values($tiemposPorMotivo)); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                };

                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let label = tooltipItem.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += Math.round(tooltipItem.raw * 100) / 100;
                                        label += ' minutos';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                };

                const graficaMotivos = new Chart(ctx, config);


                const meses = <?php echo json_encode($meses); ?>;
                const finalizados = <?php echo json_encode($finalizados); ?>;

                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('miGrafica').getContext('2d');
                    const miGrafica = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: meses, // Etiquetas para el eje X (nombres de los meses)
                            datasets: [{
                                label: 'Maquinados Finalizados',
                                data: finalizados, // Datos para el eje Y (cantidad de maquinados finalizados)
                                borderColor: 'rgba(75, 192, 192, 1)', // Color de la línea
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color del área debajo de la línea
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1 // Suaviza la línea
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Mes'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Cantidad de Maquinados Finalizados'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
</body>

</html>