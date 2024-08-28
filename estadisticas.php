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
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Estadísticas' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-start mt-5 mb-5">
                    <div class="col-12 mb-3">
                        <div class="card-header">
                            <h2>ESTADÍSTICAS</h2>
                        </div>
                        <div class="row card-body">
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
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="card-header mb-3">
                            <h2>PROYECTOS</h2>
                        </div>
                        <div class="card-body">
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
                                    $query = "SELECT * FROM proyecto WHERE estatus = 1 ORDER BY prioridad ASC";
                                    $query_run = mysqli_query($con, $query);
                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $registro) {
                                    ?>
                                            <tr class="text-center">
                                                <td style="width: 10px;">
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
                    </div>

                    <div class="col-8 mt-5">
                        <div class="card-header">
                            <h4>MAQUINADOS</h4>
                        </div>
                        <div class="card-body">
                            <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Maquinados finalizados</th>
                                        <th>Maquinados pendientes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta para obtener usuarios con rol = 8 y contar los maquinados finalizados y pendientes
                                    $query = "
                SELECT 
                    u.codigo,
                    u.nombre,
                    u.apellidop,
                    u.apellidom,
                    COUNT(CASE WHEN p.estatusplano = 0 THEN 1 END) AS maquinados_finalizados,
                    COUNT(CASE WHEN p.estatusplano IN (1, 2, 3) THEN 1 END) AS maquinados_pendientes
                FROM 
                    usuarios u
                LEFT JOIN 
                    asignacionplano a ON u.codigo = a.codigooperador
                LEFT JOIN 
                    plano p ON a.idplano = p.id
                WHERE 
                    u.rol = 8
                GROUP BY 
                    u.codigo
                ";

                                    $query_run = mysqli_query($con, $query);

                                    $finalizados = [];
                                    $pendientes = [];
                                    $usuarios = [];
                                    $total_finalizados = 0;
                                    $total_pendientes = 0;

                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $registro) {
                                            $finalizados[] = $registro['maquinados_finalizados'];
                                            $pendientes[] = $registro['maquinados_pendientes'];
                                            $usuarios[] = $registro['nombre'] . ' ' . $registro['apellidop'] . ' ' . $registro['apellidom'];
                                            $total_finalizados += $registro['maquinados_finalizados'];
                                            $total_pendientes += $registro['maquinados_pendientes'];
                                    ?>
                                            <tr>
                                                <td><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></td>
                                                <td class="text-center"><?= $registro['maquinados_finalizados']; ?></td>
                                                <td class="text-center"><?= $registro['maquinados_pendientes']; ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<td colspan='4'><p>No se encontró ningún usuario</p></td>";
                                    }

                                    // Calcular porcentajes
                                    $porcentajes_finalizados = array_map(function ($value) use ($total_finalizados) {
                                        return $total_finalizados > 0 ? round(($value / $total_finalizados) * 100, 2) : 0;
                                    }, $finalizados);

                                    $porcentajes_pendientes = array_map(function ($value) use ($total_pendientes) {
                                        return $total_pendientes > 0 ? round(($value / $total_pendientes) * 100, 2) : 0;
                                    }, $pendientes);

                                    $colors = [];
                                    for ($i = 0; $i < count($usuarios); $i++) {
                                        $colors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-4 mt-5">
                        <canvas id="miGrafica"></canvas>
                    </div>

                    <div class="col-4 mt-5">
                        <canvas id="miGraficaDos"></canvas>
                    </div>

                    <div class="col-8 mt-5">
                        <div class="card-header">
                            <h4>Ensambles</h4>
                        </div>
                        <div class="card-body">
                            <table id="miTablaDos" class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Ensambles finalizados</th>
                                        <th>Ensambles pendientes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta para obtener usuarios con rol = 4 y contar los ensambles finalizados y pendientes
                                    $query = "
                SELECT 
                    u.codigo,
                    u.nombre,
                    u.apellidop,
                    u.apellidom,
                    COUNT(CASE WHEN p.estatusplano = 0 THEN 1 END) AS ensambles_finalizados,
                    COUNT(CASE WHEN p.estatusplano IN (1, 2, 3) THEN 1 END) AS ensambles_pendientes
                FROM 
                    usuarios u
                LEFT JOIN 
                    asignaciondiagrama a ON u.codigo = a.codigooperador
                LEFT JOIN 
                    diagrama p ON a.idplano = p.id
                WHERE 
                    u.rol = 4
                GROUP BY 
                    u.codigo
                ";

                                    $query_run = mysqli_query($con, $query);

                                    $finalizadosEnsambles = [];
                                    $pendientesEnsambles = [];
                                    $usuariosEnsambles = [];

                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $registro) {
                                            $finalizadosEnsambles[] = $registro['ensambles_finalizados'];
                                            $pendientesEnsambles[] = $registro['ensambles_pendientes'];
                                            $usuariosEnsambles[] = $registro['nombre'] . ' ' . $registro['apellidop'] . ' ' . $registro['apellidom'];
                                    ?>
                                            <tr>
                                                <td><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></td>
                                                <td class="text-center"><?= $registro['ensambles_finalizados']; ?></td>
                                                <td class="text-center"><?= $registro['ensambles_pendientes']; ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<td colspan='4'><p>No se encontró ningún usuario</p></td>";
                                    }

                                    $colorsEnsambles = [];
                                    for ($i = 0; $i < count($usuariosEnsambles); $i++) {
                                        $colorsEnsambles[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 mt-5 slickcard bg-dark" id="operadores" style="border-radius: 5px;">
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
                                            <div style="min-height: 50px;">
                                                <h5 class="card-title"><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></h5>
                                            </div>
                                            <p style="color: #000;margin-left:0px;" class="card-text">
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
                                                                                                                                                        ?>
                                                <a href="estadisticaoperadores.php?id=<?= $registro['id']; ?>" class="btn btn-dark">Ver analítica</a><?php
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
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

        // Obtener los datos desde PHP
        var finalizados = <?= json_encode($finalizados); ?>;
        var pendientes = <?= json_encode($pendientes); ?>;
        var usuarios = <?= json_encode($usuarios); ?>;
        var colors = <?= json_encode($colors); ?>;

        var ctx = document.getElementById('miGrafica').getContext('2d');

        // Crear la gráfica multi-series pie chart
        var miGrafica = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: usuarios,
                datasets: [{
                        label: 'Maquinados Finalizados',
                        data: finalizados,
                        backgroundColor: colors, // Colores generados dinámicamente
                        borderColor: colors.map(color => color.replace('0.6', '1')),
                        borderWidth: 1
                    },
                    {
                        label: 'Maquinados Pendientes',
                        data: pendientes,
                        backgroundColor: colors, // Mismos colores para pendientes
                        borderColor: colors.map(color => color.replace('0.6', '1')),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' maquinados';
                            }
                        }
                    }
                }
            }
        });

        // Obtener los datos desde PHP
        var finalizadosEnsambles = <?= json_encode($finalizadosEnsambles); ?>;
        var pendientesEnsambles = <?= json_encode($pendientesEnsambles); ?>;
        var usuariosEnsambles = <?= json_encode($usuariosEnsambles); ?>;
        var colorsEnsambles = <?= json_encode($colorsEnsambles); ?>;


        var ctx = document.getElementById('miGraficaDos').getContext('2d');

        // Crear la gráfica multi-series pie chart
        var miGraficaDos = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: usuariosEnsambles,
                datasets: [{
                        label: 'Ensambles Finalizados',
                        data: finalizadosEnsambles,
                        backgroundColor: colorsEnsambles, // Colores generados dinámicamente
                        borderColor: colorsEnsambles.map(color => color.replace('0.6', '1')),
                        borderWidth: 1
                    },
                    {
                        label: 'Ensambles Pendientes',
                        data: pendientesEnsambles,
                        backgroundColor: colorsEnsambles, // Mismos colores para pendientes
                        borderColor: colorsEnsambles.map(color => color.replace('0.6', '1')),
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' ensambles';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>