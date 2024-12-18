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
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
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
                <div class="row justify-content-center align-items-center mb-5">
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="row align-items-center p-4" method="get">
                                    <!-- Agrega un campo oculto para enviar el id del usuario -->
                                    <input type="hidden" name="id" value="<?php echo $registro_id; ?>">

                                    <div class="form-floating col-3">
                                        <input class="form-control" type="date" id="fecha_inicio" name="fecha_inicio" placeholder="Fecha de inicio:" required>
                                        <label for="fecha_inicio">Fecha de inicio:</label>
                                    </div>

                                    <div class="form-floating col-3">
                                        <input class="form-control" type="date" id="fecha_fin" name="fecha_fin" placeholder="Fecha de fin:" required>
                                        <label for="fecha_fin">Fecha de fin:</label>
                                    </div>
                                    <div class="col-3">
                                        <button class="btn btn-outline-dark" type="submit">Filtrar</button>
                                        <a href="estadisticaoperadores.php?id=<?= $registro['id']; ?>" class="btn btn-warning">Limpiar</a>
                                    </div>
                                </form>

                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Motivo/Actividad</th>
                                            <th>Tiempo en paro (minutos)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
                                        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
                                        $query_paro = "SELECT 
                        motivoactividad,
                        SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_total
                    FROM historialoperadores 
                    WHERE idcodigo ='$codigouser'AND motivoactividad <> 'Inicio' AND motivoactividad <> 'Fin de jornada laboral' AND motivoactividad <> 'Atención a otra prioridad' AND horareinicio <> ''AND fechareinicio IS NOT NULL ";

                                        // Si se han seleccionado fechas, agregar condiciones de rango de fecha a la consulta SQL
                                        if ($fecha_inicio && $fecha_fin) {
                                            // Agregar condiciones de rango de fecha
                                            $query_paro .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                                        }

                                        $query_paro .= " GROUP BY motivoactividad";

                                        $query_run_paro = mysqli_query($con, $query_paro);
                                        $total_paro = 0;

                                        if (mysqli_num_rows($query_run_paro) > 0) {
                                            foreach ($query_run_paro as $registro) {
                                                // Convertir minutos totales a horas y minutos
                                                $horas = floor($registro['tiempo_total'] / 60);
                                                $minutos = $registro['tiempo_total'] % 60;
                                        ?>
                                                <tr>
                                                    <td><?= $registro['motivoactividad']; ?></td>
                                                    <td><?= $horas . " h " . $minutos . " min"; ?></td>
                                                </tr>
                                        <?php
                                                $total_paro += $registro['tiempo_total'];
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        // Convertir el tiempo total de paro a horas y minutos
                                        $total_horas = floor($total_paro / 60);
                                        $total_minutos = $total_paro % 60;
                                        ?>
                                        <tr style="background-color: #c9c9c9;">
                                            <td><b class="float-end small">Tiempo de paro total:</b></td>
                                            <td id="paro"><?= $total_horas . " horas " . $total_minutos . " minutos"; ?></td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="col-4 mt-5">
                        <canvas id="myChart"></canvas>
                    </div>
                    <div class="col-6 text-center mt-5">
                        <?php
                        $query_maquinado = "SELECT motivoactividad,
                        SUM(ABS(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio)))) AS tiempo_maquinado
                    FROM historialoperadores 
                    WHERE idcodigo ='$codigouser'AND motivoactividad = 'Inicio' AND fechareinicio IS NOT NULL ";


                        if ($fecha_inicio && $fecha_fin) {
                            $query_maquinado .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        }

                        $query_run_maquinado = mysqli_query($con, $query_maquinado);
                        $tiempo_maquinado = 0;

                        if (mysqli_num_rows($query_run_maquinado) > 0) {
                            $registro_maquinado = mysqli_fetch_assoc($query_run_maquinado);
                            $tiempo_maquinado = $registro_maquinado['tiempo_maquinado'];

                            $query_paro = "SELECT motivoactividad,
                            SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_total
                        FROM historialoperadores 
                        WHERE idcodigo ='$codigouser'
                            AND motivoactividad <> 'Inicio' AND fechareinicio IS NOT NULL";

                            if ($fecha_inicio && $fecha_fin) {
                                $query_paro .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                            }

                            $query_paro .= " GROUP BY motivoactividad";

                            $query_run_paro = mysqli_query($con, $query_paro);
                            $total_paro = 0;
                            if (mysqli_num_rows($query_run_paro) > 0) {
                                // Imprimir encabezado de la tabla
                                echo "<p><b>REGISTROS CONTEMPLADOS PARA TIEMPO DE PARO:</b></p>";
                                echo "<table border='1'>";
                                echo "<tr><th>Motivo de Actividad</th><th>Tiempo Total</th></tr>";
                            
                                // Iterar sobre los resultados de la consulta de paro
                                foreach ($query_run_paro as $registro) {
                                    // Convertir tiempo total de paro a horas y minutos
                                    $horas_paro = floor($registro['tiempo_total'] / 60);
                                    $minutos_paro = $registro['tiempo_total'] % 60;
                            
                                    // Imprimir cada registro
                                    echo "<tr>";
                                    echo "<td>" . $registro['motivoactividad'] . "</td>";
                                    echo "<td>" . $horas_paro . " horas " . $minutos_paro . " minutos</td>";
                                    echo "</tr>";
                                    $total_paro += $registro['tiempo_total'];
                                }
                                // Cerrar la tabla
                                echo "</table>";
                            }
                            
                            if (mysqli_num_rows($query_run_maquinado) > 0) {
                                // Imprimir encabezado de la tabla
                                echo "<p><b>REGISTROS CONTEMPLADOS PARA TIEMPO DE MAQUINADO:</b></p>";
                                echo "<table border='1'>";
                                echo "<tr><th>Motivo de Actividad</th><th>Tiempo Total</th></tr>";
                            
                                // Iterar sobre los resultados de la consulta de maquinado
                                foreach ($query_run_maquinado as $registro) {
                                    // Convertir tiempo total de maquinado a horas y minutos
                                    $horas_maquinado = floor($registro['tiempo_maquinado'] / 60);
                                    $minutos_maquinado = $registro['tiempo_maquinado'] % 60;
                            
                                    // Imprimir cada registro
                                    echo "<tr>";
                                    echo "<td>" . $registro['motivoactividad'] . "</td>";
                                    echo "<td>" . $horas_maquinado . " horas " . $minutos_maquinado . " minutos</td>";
                                    echo "</tr>";
                                    $tiempo_maquinado += $registro['tiempo_maquinado'];
                                }
                                // Cerrar la tabla
                                echo "</table>";
                            }
                            

                            if (mysqli_num_rows($query_run_paro) > 0) {
                                foreach ($query_run_paro as $registro) {
                                    $total_paro += $registro['tiempo_total'];
                                }
                            }

                            $tiempo_maquinado = $tiempo_maquinado - $total_paro;

                            $horas = floor($tiempo_maquinado / 60);
                            $minutos = $tiempo_maquinado % 60;
                        ?>

                            <p><b>TIEMPO TOTAL DE MAQUINADO</b></p>
                            <p style="font-size: 80px;"><?= $horas; ?> h <?= $minutos; ?> min</p>

                        <?php
                        } else {
                            echo "<p>No se encontró información suficiente para calcular el tiempo total de maquinado.</p>";
                        }
                        ?>
                    </div>

                    <?php

                    // Calcular piezas totales
                    $query_piezas_totales = "SELECT SUM(piezas) AS piezas_totales FROM plano p 
                            INNER JOIN asignacionplano a ON p.id = a.idplano 
                            WHERE a.codigooperador = '$codigouser'";
                    $query_run_piezas_totales = mysqli_query($con, $query_piezas_totales);
                    $piezas_totales = 0;

                    if (mysqli_num_rows($query_run_piezas_totales) > 0) {
                        $registro_piezas_totales = mysqli_fetch_assoc($query_run_piezas_totales);
                        $piezas_totales = $registro_piezas_totales['piezas_totales'];
                    }

                    // Calcular piezas terminadas
                    $query_piezas_terminadas = "SELECT SUM(piezas) AS piezas_terminadas FROM plano p 
                                INNER JOIN asignacionplano a ON p.id = a.idplano 
                                WHERE a.codigooperador = '$codigouser' AND p.estatusplano = 0";
                    $query_run_piezas_terminadas = mysqli_query($con, $query_piezas_terminadas);
                    $piezas_terminadas = 0;

                    if (mysqli_num_rows($query_run_piezas_terminadas) > 0) {
                        $registro_piezas_terminadas = mysqli_fetch_assoc($query_run_piezas_terminadas);
                        $piezas_terminadas = $registro_piezas_terminadas['piezas_terminadas'];
                    }

                    ?>


                </div>
                <div class="row bg-dark p-5 mb-3">
                    <div class="col-6 text-center">
                        <p style="color: #fff;font-size:18px;">Piezas asignadas: <br><?php echo $piezas_totales; ?></p>
                    </div>

                    <div class="col-6 text-center">
                        <p style="color: #fff;font-size:18px;">Piezas terminadas: <br><?php echo $piezas_terminadas; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Obtener los datos para el gráfico
        var motivos = [];
        var tiempos = [];
        <?php
        $query = "SELECT 
                        motivoactividad,
                        SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_total
                    FROM historialoperadores 
                    WHERE idcodigo ='$codigouser' AND motivoactividad <> 'Inicio' AND motivoactividad <> 'Fin de jornada laboral' AND motivoactividad <> 'Atención a otra prioridad' AND horareinicio <> '' AND fechareinicio IS NOT NULL ";

        // Si se han seleccionado fechas, agregar condiciones de rango de fecha a la consulta SQL
        if ($fecha_inicio && $fecha_fin) {
            // Agregar condiciones de rango de fecha
            $query .= " AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        }

        $query .= " GROUP BY motivoactividad";
        $query_run = mysqli_query($con, $query);
        if (mysqli_num_rows($query_run) > 0) {
            foreach ($query_run as $registro) {
        ?>
                motivos.push("<?php echo $registro['motivoactividad']; ?>");
                tiempos.push(<?php echo $registro['tiempo_total']; ?>);
        <?php
            }
        }
        ?>

        // Calcular el tiempo total
        var tiempoTotal = tiempos.reduce((total, tiempo) => total + tiempo, 0);

        // Calcular el porcentaje de tiempo para cada motivo de actividad
        var porcentajes = tiempos.map(tiempo => tiempo / tiempoTotal * 100);

        // Crear el gráfico
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: motivos,
                datasets: [{
                    data: porcentajes,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                        // Puedes agregar más colores si tienes más motivos de actividad
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Distribución del tiempo por motivo de actividad'
                    }
                }
            }
        });
    </script>
</body>

</html>