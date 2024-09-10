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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Estadística maquinado / actividad' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Estadística Plano | Solara</title>
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">ESTADÍSTICA PLANO
                                    <?php
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM plano WHERE id ='$registro_id'";
                                    $query_run = mysqli_query($con, $query);
                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_assoc($query_run);
                                        echo $registro['nombreplano'];
                                        $siguiente_id = $registro['id'] + 1;
                                        $anterior_id = $registro['id'] - 1; // Obtener el siguiente registro id
                                        echo '<a href="estadisticaplano.php?id=' . $siguiente_id . '" class="btn btn-primary btn-sm float-end m-1">Siguiente</a>';
                                        echo '<a href="estadisticaplano.php?id=' . $anterior_id . '" class="btn btn-secondary btn-sm float-end m-1">Anterior</a>';
                                    } else {
                                        echo "Error"; // Si no se encuentra un nombre de plano, muestra un texto predeterminado
                                    }
                                    ?>
                                    <a href="estadisticas.php" class="btn btn-danger btn-sm float-end m-1">
                                        Salir
                                    </a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Motivo/Actividad</th>
                                            <th>Tiempo en paro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT 
                                        motivoactividad,
                                        SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_total
                                        FROM historialoperadores 
                                        WHERE idplano ='$registro_id'
                                        AND motivoactividad <> 'Inicio' 
                                        GROUP BY motivoactividad";

                                        $query_run = mysqli_query($con, $query);
                                        $total_paro = 0;

                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                                // Convertir los minutos a horas y minutos
                                                $horas = floor($registro['tiempo_total'] / 60);
                                                $minutos = $registro['tiempo_total'] % 60;
                                        ?>
                                                <tr>
                                                    <td><?= $registro['motivoactividad']; ?></td>
                                                    <td><?= $horas . "h " . $minutos . "m"; ?></td>
                                                </tr>
                                        <?php
                                                // Incrementar el total de paro
                                                $total_paro += $registro['tiempo_total'];
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        // Convertir el total de paro a horas y minutos
                                        $total_horas = floor($total_paro / 60);
                                        $total_minutos = $total_paro % 60;
                                        ?>
                                        <tr style="background-color: #c9c9c9;">
                                            <td><b class="float-end small">Tiempo de paro total:</b></td>
                                            <td id="paro"><?= $total_horas . "h " . $total_minutos . "m"; ?></td>
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
                        // Consulta para obtener el tiempo total de "Inicio"
                        $query_inicio = "SELECT 
                     SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fecha, ' ', hora))) AS tiempo_inicio
                     FROM historialoperadores 
                     WHERE idplano ='$registro_id'
                     AND motivoactividad = 'Inicio'";

                        $query_run_inicio = mysqli_query($con, $query_inicio);

                        // Consulta para obtener el tiempo total de "Fin de Inicio"
                        $query_fin = "SELECT 
                 SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_fin
                 FROM historialoperadores 
                 WHERE idplano ='$registro_id'
                 AND motivoactividad = 'Inicio'";

                        $query_run_fin = mysqli_query($con, $query_fin);

                        // Verificar si se encontraron registros para ambas consultas
                        if (mysqli_num_rows($query_run_inicio) > 0 && mysqli_num_rows($query_run_fin) > 0) {
                            $registro_inicio = mysqli_fetch_assoc($query_run_inicio);
                            $registro_fin = mysqli_fetch_assoc($query_run_fin);

                            // Calcular el tiempo total de maquinado restando el tiempo de "Fin de Inicio" del tiempo de "Inicio"
                            $tiempo_maquinado_minutos = $registro_fin['tiempo_fin'] - $registro_inicio['tiempo_inicio'] - $total_paro;

                            // Convertir minutos a horas y minutos
                            $horas = floor($tiempo_maquinado_minutos / 60);
                            $minutos = $tiempo_maquinado_minutos % 60;
                        ?>

                            <p><b>TIEMPO TOTAL DE MAQUINADO</b></p>
                            <p style="font-size: 40px;"><?= $horas ?> h <?= $minutos ?> min</p>

                        <?php
                        } else {
                            echo "<p>No se encontró información suficiente para calcular el tiempo total de maquinado.</p>";
                        }
                        ?>
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
   WHERE idplano ='$registro_id'
   AND motivoactividad <> 'Inicio' AND motivoactividad <> 'Fin de jornada laboral'
   GROUP BY motivoactividad";
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