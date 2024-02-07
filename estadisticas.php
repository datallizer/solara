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

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" href="css/slickslider.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <title>Estadísticas | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-start mt-5 mb-5">
                    <div class="col-12">
                        <h2 class="mb-3">ESTADÍSTICAS</h2>
                    </div>
                    <div class="col-2 p-3 cardstats m-1">
                        <div class="row">
                            <div class="col-9">
                                <h3>PROYECTOS<br>ACTIVOS</h3>
                            </div>
                            <div class="col-1"><i class="bi bi-briefcase-fill"></i></div>
                            <div class="col-12 text-center">
                                <p>
                                    <?php
                                    // Consulta para obtener el número total de proyectos con estatus 1
                                    $query = "SELECT COUNT(*) as total_proyectos FROM proyecto WHERE estatus = 1";
                                    $result = mysqli_query($con, $query);

                                    if ($result->num_rows >= 0) {
                                        // Obtener el resultado de la consulta
                                        $row = $result->fetch_assoc();

                                        // Mostrar el resultado en un párrafo
                                        echo $row["total_proyectos"];
                                    } else {
                                        echo "Error";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col p-3 cardstats m-1">
                        <div class="row">
                            <div class="col-9">
                                <h3>PLANOS<br>ACTIVOS</h3>
                            </div>
                            <div class="col-1"><i class="bi bi-easel-fill"></i></div>
                            <div class="col-12 text-center">
                                <p>
                                    <?php
                                    // Consulta para obtener el número total de proyectos con estatus 1
                                    $query = "SELECT COUNT(*) as total_proyectos FROM plano WHERE estatusplano <> 0";
                                    $result = mysqli_query($con, $query);

                                    if ($result->num_rows >= 0) {
                                        // Obtener el resultado de la consulta
                                        $row = $result->fetch_assoc();

                                        // Mostrar el resultado en un párrafo
                                        echo $row["total_proyectos"];
                                    } else {
                                        echo "Error";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col p-3 cardstats m-1">
                        <div class="row">
                            <div class="col-9">
                                <h3>ENSAMBLES<br>ACTIVOS</h3>
                            </div>
                            <div class="col-1"><i class="bi bi-gear-wide-connected"></i></div>
                            <div class="col-12 text-center">
                                <p>
                                    <?php
                                    // Consulta para obtener el número total de proyectos con estatus 1
                                    $query = "SELECT COUNT(*) as total_proyectos FROM diagrama WHERE estatusplano <> 0";
                                    $result = mysqli_query($con, $query);

                                    if ($result->num_rows >= 0) {
                                        // Obtener el resultado de la consulta
                                        $row = $result->fetch_assoc();

                                        // Mostrar el resultado en un párrafo
                                        echo $row["total_proyectos"];
                                    } else {
                                        echo "Error";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 p-3 cardstats m-1">
                        <div class="row">
                            <div class="col-9">
                                <h3>QUOTES A<br>REVISAR</h3>
                            </div>
                            <div class="col-1"><i class="bi bi-clipboard-check-fill"></i></div>
                            <div class="col-12 text-center">
                                <p>
                                    <?php
                                    // Consulta para obtener el número total de proyectos con estatus 1
                                    $query = "SELECT COUNT(*) as total_proyectos FROM quotes WHERE estatusq = 1";
                                    $result = mysqli_query($con, $query);

                                    if ($result->num_rows >= 0) {
                                        // Obtener el resultado de la consulta
                                        $row = $result->fetch_assoc();

                                        // Mostrar el resultado en un párrafo
                                        echo $row["total_proyectos"];
                                    } else {
                                        echo "Error";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 p-3 cardstats m-1">
                        <div class="row">
                            <div class="col-10">
                                <h3>COMPRAS<br>PENDIENTES</h3>
                            </div>
                            <div class="col-1"><i class="bi bi-cart-check-fill"></i></div>
                            <div class="col-12 text-center">
                                <p>
                                    <?php
                                    // Consulta para obtener el número total de proyectos con estatus 1
                                    $query = "SELECT COUNT(*) as total_proyectos FROM quotes WHERE estatusq = 0";
                                    $result = mysqli_query($con, $query);

                                    if ($result->num_rows >= 0) {
                                        // Obtener el resultado de la consulta
                                        $row = $result->fetch_assoc();

                                        // Mostrar el resultado en un párrafo
                                        echo $row["total_proyectos"];
                                    } else {
                                        echo "Error";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Proyecto</th>
                                    <th>Prioridad</th>
                                    <th>Piezas totales</th>
                                    <th>Piezas asignadas</th>
                                    <th>Piezas terminadas</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM proyecto ORDER BY prioridad ASC";
                                $query_run = mysqli_query($con, $query);
                                if (mysqli_num_rows($query_run) > 0) {
                                    foreach ($query_run as $registro) {
                                ?>
                                        <tr class="text-center">
                                            <td>
                                                <p class="text-center"><?= $registro['id']; ?></p>
                                            </td>
                                            <td>
                                                <p class="text-center"><?= $registro['nombre']; ?></p>
                                            </td>
                                            <?php
                                            if ($registro['prioridad'] == 1) {
                                                echo "<td style='background-color: #ff0000;color:#fff;'>" . $registro['prioridad'] . "</td>"; // Rojo oscuro
                                            } elseif ($registro['prioridad'] == 2) {
                                                echo "<td style='background-color: #ff1a1a;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 3) {
                                                echo "<td style='background-color: #ff3333;'>" . $registro['prioridad'] . "</td>"; // Rojo medio
                                            } elseif ($registro['prioridad'] == 4) {
                                                echo "<td style='background-color: #ff4d4d;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 5) {
                                                echo "<td style='background-color: #ff6666;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 6) {
                                                echo "<td style='background-color: #ff8080;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 7) {
                                                echo "<td style='background-color: #ff9999;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 8) {
                                                echo "<td style='background-color: #ffb2b2;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 9) {
                                                echo "<td style='background-color: #ffcccc;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 10) {
                                                echo "<td style='background-color: #ffe5e5;'>" . $registro['prioridad'] . "</td>"; // Rojo claro
                                            } elseif ($registro['prioridad'] == 11) {
                                                echo "<td style='background-color: #ffffb3;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 12) {
                                                echo "<td style='background-color: #ffff99;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 13) {
                                                echo "<td style='background-color: #ffff80;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 14) {
                                                echo "<td style='background-color: #ffff66;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 15) {
                                                echo "<td style='background-color: #ffff4d;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 16) {
                                                echo "<td style='background-color: #ffff33;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 17) {
                                                echo "<td style='background-color: #ffff1a;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 18) {
                                                echo "<td style='background-color: #ffff00;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 19) {
                                                echo "<td style='background-color: #ffff00;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 20) {
                                                echo "<td style='background-color: #e5e500;'>" . $registro['prioridad'] . "</td>"; // Amarillo claro
                                            } elseif ($registro['prioridad'] == 21) {
                                                echo "<td style='background-color: #c6e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 22) {
                                                echo "<td style='background-color: #a8e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 23) {
                                                echo "<td style='background-color: #89e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 24) {
                                                echo "<td style='background-color: #67e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 25) {
                                                echo "<td style='background-color: #58e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 26) {
                                                echo "<td style='background-color: #39e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 27) {
                                                echo "<td style='background-color: #26e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 28) {
                                                echo "<td style='background-color: #00e500;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 29) {
                                                echo "<td style='background-color: #00e51b;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } elseif ($registro['prioridad'] == 30) {
                                                echo "<td style='background-color: #00e539;'>" . $registro['prioridad'] . "</td>"; // Verde claro
                                            } else {
                                                echo "<td>" . $registro['prioridad'] . "</td>"; // Valor fuera del rango
                                            }
                                            ?>

                                            <td>
                                                <?php
                                                $idProyecto = $registro['id'];
                                                $querySumaPiezas = "SELECT SUM(piezas) AS suma_piezas FROM plano WHERE idproyecto = $idProyecto";
                                                $querySumaPiezasResult = mysqli_query($con, $querySumaPiezas);

                                                if ($querySumaPiezasResult) {
                                                    $sumaPiezas = mysqli_fetch_assoc($querySumaPiezasResult)['suma_piezas'];
                                                    echo $sumaPiezas;
                                                } else {
                                                    echo "Error al obtener la suma de piezas, contacte a soporte";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $idProyecto = $registro['id'];
                                                // Consulta para obtener la suma de piezas asignadas (considerando solo valores únicos de idplano)
                                                $querySumaAsignadas = "SELECT SUM(p.piezas) AS suma_asignadas
                                              FROM plano p
                                              INNER JOIN (
                                                  SELECT DISTINCT idplano
                                                  FROM asignacionplano
                                              ) a ON p.id = a.idplano
                                              WHERE p.idproyecto = $idProyecto";
                                                $querySumaAsignadasResult = mysqli_query($con, $querySumaAsignadas);

                                                if ($querySumaAsignadasResult) {
                                                    $sumaAsignadas = mysqli_fetch_assoc($querySumaAsignadasResult)['suma_asignadas'];
                                                    echo $sumaAsignadas;
                                                } else {
                                                    echo "Error al obtener la suma de piezas asignadas, contacte a soporte";
                                                }
                                                ?>
                                            </td>
                                            </td>
                                            <?php
                                            $idProyecto = $registro['id'];
                                            $querySumaPiezas = "SELECT SUM(piezas) AS suma_piezas FROM plano WHERE idproyecto = $idProyecto AND estatusplano = 0";
                                            $querySumaPiezasResult = mysqli_query($con, $querySumaPiezas);

                                            if ($querySumaPiezasResult) {
                                                $sumaPiezas = mysqli_fetch_assoc($querySumaPiezasResult)['suma_piezas'];
                                                echo "<td>$sumaPiezas</td>";
                                            } else {
                                                echo "<td>Error al obtener la suma de piezas, contacte a soporte</td>";
                                            }
                                            ?>
                                            <td>
                                                <div class="progreso text-center" style="border: 1px solid #828282;"></div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<td colspan='7'><p>No se encontro ningun registro</p></td>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6 mt-3">
                        <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="background-color: #2c5b87;color:#fff;width: 10px;">ID</th>
                                    <th style="background-color: #2c5b87;color:#fff;">TIEMPO</th>
                                    <th style="background-color: #2c5b87;color:#fff;">PLANOS/ACTIVIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $query = "SELECT * FROM plano WHERE estatusplano <> 1 ORDER BY id DESC";
                                $query_run = mysqli_query($con, $query);
                                if (mysqli_num_rows($query_run) > 0) {
                                    foreach ($query_run as $registro) {
                                        $registro_id = $registro['id'];
                                ?>
                                        <tr>
                                            <td><?= $registro['id']; ?></td>
                                            <td>
                                                <?php
                                                // Consulta para obtener el tiempo total de "Inicio"
                                                $query_inicio = "SELECT 
                                                SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_inicio FROM historialoperadores WHERE idplano ='$registro_id' AND motivoactividad = 'Inicio'";

                                                $query_run_inicio = mysqli_query($con, $query_inicio);

                                                // Consulta para obtener el tiempo total de "Fin de jornada laboral"
                                                $query_fin = "SELECT 
                     SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_fin
                 FROM historialoperadores 
                 WHERE idplano ='$registro_id'
                 AND motivoactividad = 'Fin de jornada laboral'";

                                                $query_run_fin = mysqli_query($con, $query_fin);
                                                if (mysqli_num_rows($query_run_inicio) > 0 && mysqli_num_rows($query_run_fin) > 0) {
                                                    $registro_inicio = mysqli_fetch_assoc($query_run_inicio);
                                                    $registro_fin = mysqli_fetch_assoc($query_run_fin);

                                                    // Calcular la diferencia en minutos entre el tiempo de inicio y el tiempo de fin
                                                    $diferencia_minutos = $registro_inicio['tiempo_inicio'] - $registro_fin['tiempo_fin'];

                                                    // Convertir minutos a horas y minutos
                                                    $horas = floor($diferencia_minutos / 60);
                                                    $minutos = $diferencia_minutos % 60;
                                                ?>
                                                    <p><?= $horas ?> h <?= $minutos ?> min</p>
                                                <?php
                                                } else {
                                                    echo "<p>No se encontró información suficiente para calcular el tiempo total de maquinado.</p>";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a style="text-decoration: none;color: #3f3f3f;" href="estadisticaplano.php?id=<?= $registro['id']; ?>">
                                                    <div class="row">
                                                        <div class="col"><?= $registro['nombreplano']; ?></div>
                                                        <div class="col"><i class="bi bi-chevron-right" style="margin-left: 100px;"></i></div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<td colspan='2'><p>No se encontro ningun registro</p></td>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6 mt-3">
                        <table id="miTablaTres" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="background-color: #2c5b87;color:#fff;width: 10px;">ID</th>
                                    <th style="background-color: #2c5b87;color:#fff;">TIEMPO</th>
                                    <th style="background-color: #2c5b87;color:#fff;">DIAGRAMA/ACTIVIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $query = "SELECT * FROM diagrama WHERE estatusplano <> 1 ORDER BY id DESC";
                                $query_run = mysqli_query($con, $query);
                                if (mysqli_num_rows($query_run) > 0) {
                                    foreach ($query_run as $registro) {
                                        $registro_id = $registro['id'];
                                ?>
                                        <tr>
                                            <td><?= $registro['id']; ?></td>
                                            <td>
                                                <?php
                                                // Consulta para obtener el tiempo total de "Inicio"
                                                $query_inicio = "SELECT 
                                                SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_inicio FROM historialensamble WHERE idplano ='$registro_id' AND motivoactividad = 'Inicio'";

                                                $query_run_inicio = mysqli_query($con, $query_inicio);

                                                // Consulta para obtener el tiempo total de "Fin de jornada laboral"
                                                $query_fin = "SELECT 
                                                SUM(TIMESTAMPDIFF(MINUTE, CONCAT(fecha, ' ', hora), CONCAT(fechareinicio, ' ', horareinicio))) AS tiempo_fin
                                                FROM historialensamble WHERE idplano ='$registro_id'
                                                AND motivoactividad = 'Fin de jornada laboral'";

                                                $query_run_fin = mysqli_query($con, $query_fin);
                                                if (mysqli_num_rows($query_run_inicio) > 0 && mysqli_num_rows($query_run_fin) > 0) {
                                                    $registro_inicio = mysqli_fetch_assoc($query_run_inicio);
                                                    $registro_fin = mysqli_fetch_assoc($query_run_fin);

                                                    // Calcular la diferencia en minutos entre el tiempo de inicio y el tiempo de fin
                                                    $diferencia_minutos = $registro_inicio['tiempo_inicio'] - $registro_fin['tiempo_fin'];

                                                    // Convertir minutos a horas y minutos
                                                    $horas = floor($diferencia_minutos / 60);
                                                    $minutos = $diferencia_minutos % 60;
                                                ?>
                                                    <p><?= $horas ?> h <?= $minutos ?> min</p>
                                                <?php
                                                } else {
                                                    echo "<p>No se encontró información suficiente para calcular el tiempo total de maquinado.</p>";
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <a style="text-decoration: none;color: #3f3f3f;" href="estadisticaensamble.php?id=<?= $registro['id']; ?>">
                                                    <div class="row">
                                                        <div class="col"><?= $registro['nombreplano']; ?></div>
                                                        <div class="col"><i class="bi bi-chevron-right" style="margin-left: 100px;"></i></div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<td colspan='2'><p>No se encontro ningun registro</p></td>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 mt-3 slickcard bg-dark" id="operadores">
                        <?php

                        $query = "SELECT * FROM usuarios WHERE rol = 8 OR rol = 4 ORDER BY id DESC";
                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $registro) {
                                $registro_id = $registro['id'];
                        ?>
                                <div class="slickc">
                                    <div class="card" style="width: 100%;">
                                        <img class="card-img-top" style="object-fit:cover;height:350px;width: 100%;border-radius:5px;" src="data:image/jpeg;base64,<?php echo base64_encode($registro['medio']); ?>" alt="Foto perfil">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></h5>
                                            <p style="color: #000;" class="card-text">
                                                <?php
                                                if ($registro['rol'] === '4') {
                                                    echo "Técnico controles";
                                                } else if ($registro['rol'] === '8') {
                                                    echo "Técnico mecanico";
                                                } else {
                                                    echo "Error, contacte a soporte";
                                                }
                                                ?>
                                            </p>
                                            <?php
                                            if ($registro['rol'] === '4') {
                                            ?><a href="estadisticacontrol.php?id=<?= $registro['id']; ?>" class="btn btn-secondary">Ver analítica</a><?php
                                                                                                                                                    } else if ($registro['rol'] === '8') {
                                                                                                                                                        ?><a href="estadisticaoperadores.php?id=<?= $registro['id']; ?>" class="btn btn-dark">Ver analítica</a><?php
                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                echo "Error, contacte a soporte";
                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                ?>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "Error";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="js/slickslider.js"></script>
    <script>
        $(document).ready(function() {
            $('#miTabla, #miTablaDos, #miTablaTres').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25
            });
            // Itera sobre cada fila de la tabla
            $('#miTabla tbody tr').each(function() {
                // Obtiene los valores de las columnas "piezas terminadas" y "piezas totales" en la fila actual
                var piezasTerminadas = parseFloat($(this).find('td:eq(5)').text());
                var piezasTotales = parseFloat($(this).find('td:eq(3)').text());

                // Verifica si los valores son numéricos y si el denominador (piezasTotales) no es cero
                if (!isNaN(piezasTerminadas) && !isNaN(piezasTotales) && piezasTotales !== 0) {
                    // Calcula el porcentaje
                    var porcentaje = (piezasTerminadas / piezasTotales) * 100;

                    // Crea un nuevo elemento span para mostrar el número de porcentaje
                    var spanPorcentaje = $('<span>').text(porcentaje.toFixed(2) + '%').css('color', '#000000');

                    // Crea un nuevo div con la clase "progreso" y añade el span con el porcentaje
                    var divProgreso = $('<div>').addClass('progreso p-2').css({
                        'width': porcentaje + '%',
                        'background-color': '#829bd1', // Cambia 'blue' al color azul que desees
                        'position': 'relative'
                    }).append(spanPorcentaje);

                    // Limpia el contenido de la columna "Progreso" y añade el nuevo div
                    $(this).find('.progreso').empty().append(divProgreso);
                } else {
                    // Si hay algún problema con los valores, puedes manejarlo de acuerdo a tus necesidades
                    $(this).find('.progreso').text('0%');
                }
            });
        });
    </script>
</body>

</html>