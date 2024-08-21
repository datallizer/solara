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
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
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
                                $query = "SELECT * FROM usuarios WHERE id='$registro_id' ";
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
                    <div class="col m-3" style="background-color: #e7e7e7;padding:15px 0px;">
                        <label style="margin-left: 25px;" for="filtroPlano"><b>Filtrar por Plano/Actividad:</b></label>
                        <div id="filtroPlano" class="form-check d-flex flex-wrap mt-3" style="max-height: 60px;overflow-y:scroll;">
                            <?php
                            // Obtener todos los planos
                            $queryPlanos = "SELECT plano.*
                            FROM plano 
                            JOIN asignacionplano ON asignacionplano.idplano = plano.id 
                            JOIN usuarios ON asignacionplano.codigooperador = usuarios.codigo
                            WHERE asignacionplano.codigooperador = $codigouser 
                            AND plano.estatusplano = 0 ORDER BY plano.id DESC";
                            $resultPlanos = mysqli_query($con, $queryPlanos);
                            while ($plano = mysqli_fetch_assoc($resultPlanos)) {
                                echo '<div class="form-check me-3">';
                                echo '<input class="form-check-input" type="checkbox" value="' . $plano['id'] . '" id="plano' . $plano['id'] . '">';
                                echo '<label class="form-check-label" for="plano' . $plano['id'] . '">' . $plano['nombreplano'] . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Plano/Actividad</th>
                                            <th>Motivo</th>
                                            <th>Fecha inicio</th>
                                            <th>Hora inicio</th>
                                            <th>Fecha fin</th>
                                            <th>Hora fin</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cuerpoTabla">
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
                                                  AND plano.estatusplano = 0";

                                        // Aplicar filtro si hay planos seleccionados
                                        if (!empty($planosSeleccionados)) {
                                            $planosIds = implode(",", array_map('intval', $planosSeleccionados));
                                            $query .= " AND historialoperadores.idplano IN ($planosIds)";
                                        }

                                        $query .= " ORDER BY historialoperadores.id DESC";
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
                                        ?>
                                                <tr>
                                                    <td><?= $registro['id']; ?></td>
                                                    <td><?= $registro['nombreplano']; ?></td>
                                                    <td><?= $registro['motivoactividad']; ?></td>
                                                    <td><?= $registro['fecha']; ?></td>
                                                    <td><?= $registro['hora']; ?></td>
                                                    <td><?= $registro['fechareinicio']; ?></td>
                                                    <td><?= $registro['horareinicio']; ?></td>
                                                    <td><?= $totalTiempo; ?></td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='8'><p>No se encontró ningún usuario</p></td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="col-4 mt-3">
                        <canvas id="graficaMotivos"></canvas>
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
                $(document).ready(function() {
                    $('#miTabla').DataTable({
                        "order": [
                            [0, "desc"],
                            [1, "desc"]
                        ],
                        "pageLength": 10
                    });
                });

                function obtenerPlanosSeleccionados() {
                    const selectedCheckboxes = Array.from(document.querySelectorAll('#filtroPlano .form-check-input:checked'));
                    return selectedCheckboxes.map(checkbox => checkbox.value);
                }

                $('#filtroPlano').on('change', function() {
                    const selectedOptions = obtenerPlanosSeleccionados();
                    console.log('Planos seleccionados:', selectedOptions);
                    fetch('operadoresstatics.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                planos: selectedOptions
                            }),
                        })
                        .then(response => response.text()) // Cambia a response.text() para verificar el contenido
                        .then(text => {
                            console.log('Respuesta del servidor (texto):', text); // Verifica el contenido como texto

                            // Intenta parsear el texto como JSON
                            try {
                                const data = JSON.parse(text);
                                console.log('Respuesta del servidor (JSON):', data);

                                document.getElementById('cuerpoTabla').innerHTML = data.tabla;

                                graficaMotivos.data.labels = data.labels;
                                graficaMotivos.data.datasets[0].data = data.datos;
                                graficaMotivos.update();
                            } catch (error) {
                                console.error('Error al parsear JSON:', error);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });




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
            </script>
</body>

</html>