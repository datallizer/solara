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
                                                // Color de la prioridad
                                                $prioridadColor = [
                                                    1 => "#ff0000",
                                                    2 => "#ff1a1a",
                                                    3 => "#ff3333",
                                                    4 => "#ff4d4d",
                                                    5 => "#ff6666",
                                                    6 => "#ff8080",
                                                    7 => "#ff9999",
                                                    8 => "#ffb2b2",
                                                    9 => "#ffcccc",
                                                    10 => "#ffe5e5",
                                                    11 => "#ffffb3",
                                                    12 => "#ffff99",
                                                    13 => "#ffff80",
                                                    14 => "#ffff66",
                                                    15 => "#ffff4d",
                                                    16 => "#ffff33",
                                                    17 => "#ffff1a",
                                                    18 => "#ffff00",
                                                    19 => "#ffff00",
                                                    20 => "#e5e500",
                                                    21 => "#c6e500",
                                                    22 => "#a8e500",
                                                    23 => "#89e500",
                                                    24 => "#67e500",
                                                    25 => "#58e500",
                                                    26 => "#39e500",
                                                    27 => "#26e500",
                                                    28 => "#00e500",
                                                    29 => "#00e51b",
                                                    30 => "#00e539"
                                                ];
                                                $prioridadColor = $prioridadColor[$registro['prioridad']] ?? '#ffffff';
                                                echo "<td style='background-color: $prioridadColor;color:#fff;'>" . $registro['prioridad'] . "</td>";
                                                ?>

                                                <td>
                                                    <?php
                                                    // Piezas Totales: Consulta para obtener piezas de planos y archivoplanos
                                                    $idProyecto = $registro['id'];
                                                    $querySumaPiezas = "
                            SELECT SUM(piezas) AS suma_piezas 
                            FROM (
                                SELECT piezas FROM plano WHERE idproyecto = $idProyecto
                                UNION ALL
                                SELECT piezas FROM archivoplano WHERE idproyecto = $idProyecto
                            ) AS total_piezas";
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
                                                    // Piezas Asignadas: Sumar piezas asignadas desde la tabla asignacionplano
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

                                                <td>
                                                    <?php
                                                    // Piezas Terminadas: Sumar piezas con estatus de terminado (estatusplano = 0)
                                                    $querySumaTerminadas = "SELECT SUM(piezas) AS suma_piezas 
                                                FROM archivoplano 
                                                WHERE idproyecto = $idProyecto AND estatusplano = 0
                                                UNION ALL
                                                SELECT SUM(piezas) AS suma_piezas 
                                                FROM archivoplano 
                                                WHERE idproyecto = $idProyecto AND estatusplano = 0";
                                                    $querySumaTerminadasResult = mysqli_query($con, $querySumaTerminadas);

                                                    if ($querySumaTerminadasResult) {
                                                        $sumaTerminadas = mysqli_fetch_assoc($querySumaTerminadasResult)['suma_piezas'];
                                                        echo $sumaTerminadas;
                                                    } else {
                                                        echo "Error al obtener la suma de piezas terminadas, contacte a soporte";
                                                    }
                                                    ?>
                                                </td>

                                                <td>
                                                    <div class="progreso text-center" style="border: 1px solid #828282;"></div>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<td colspan='7'><p>No se encontró ningún registro</p></td>";
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
                            <table id="miTablaDos" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th class="text-center">Maquinados Finalizados</th>
                                        <th class="text-center">Maquinados Pendientes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Inicializar arrays para evitar errores si la consulta no devuelve resultados
                                    $finalizados = [];
                                    $pendientes = [];
                                    $usuarios = [];
                                    $colors = [];

                                    // Consulta para obtener usuarios con rol = 8 o 13 y contar los maquinados pendientes
                                    $query = "SELECT 
            u.codigo,
            u.nombre,
            u.apellidop,
            u.apellidom,
            u.estatus,
            COUNT(CASE WHEN p.estatusplano IN (1, 2, 3) THEN 1 END) AS maquinados_pendientes
        FROM 
            usuarios u
        LEFT JOIN 
            asignacionplano a ON u.codigo = a.codigooperador
        LEFT JOIN 
            plano p ON a.idplano = p.id
        WHERE 
            (u.rol = 8 OR u.rol = 13) AND u.estatus = 1
        GROUP BY 
            u.codigo";

                                    $query_finalizados = "SELECT 
            u.codigo,
            COUNT(CASE WHEN p.estatusplano = 0 THEN 1 END) AS maquinados_finalizados
        FROM 
            usuarios u
        LEFT JOIN 
            archivoasignacionplano a ON u.codigo = a.codigooperador
        LEFT JOIN 
            archivoplano p ON a.idplano = p.id
        WHERE 
            (u.rol = 8 OR u.rol = 13) AND u.estatus = 1
        GROUP BY 
            u.codigo";

                                    // Ejecutar las consultas
                                    $query_run = mysqli_query($con, $query);
                                    $query_finalizados_run = mysqli_query($con, $query_finalizados);

                                    // Guardar maquinados finalizados en un array asociativo
                                    $finalizados_data = [];
                                    while ($row_finalizados = mysqli_fetch_assoc($query_finalizados_run)) {
                                        $finalizados_data[$row_finalizados['codigo']] = $row_finalizados['maquinados_finalizados'];
                                    }

                                    $total_finalizados = 0;
                                    $total_pendientes = 0;

                                    // Procesar la consulta de maquinados pendientes
                                    if (mysqli_num_rows($query_run) > 0) {
                                        while ($registro = mysqli_fetch_assoc($query_run)) {
                                            $codigo = $registro['codigo'];
                                            $maquinados_finalizados = isset($finalizados_data[$codigo]) ? $finalizados_data[$codigo] : 0;
                                            $maquinados_pendientes = $registro['maquinados_pendientes'];

                                            $usuarios[] = $registro['nombre'] . " " . $registro['apellidop'] . " " . $registro['apellidom'];
                                            $finalizados[] = $maquinados_finalizados;
                                            $pendientes[] = $maquinados_pendientes;

                                            $total_finalizados += $maquinados_finalizados;
                                            $total_pendientes += $maquinados_pendientes;

                                            // Generar un color aleatorio para cada usuario
                                            $colors[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.6)';
                                        }
                                    }

                                    if (!empty($usuarios)) {
                                        for ($i = 0; $i < count($usuarios); $i++) {
                                            echo "<tr>
                                                <td>{$usuarios[$i]}</td>
                                                <td class='text-center'>{$finalizados[$i]}</td>
                                                <td class='text-center'>{$pendientes[$i]}</td>
                                              </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'><p>No se encontró ningún usuario</p></td></tr>";
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
                                    $query = "SELECT 
                                                u.codigo,
                                                u.nombre,
                                                u.apellidop,
                                                u.apellidom,
                                                u.estatus,
                                                COUNT(CASE WHEN p.estatusplano = 0 THEN 1 END) AS ensambles_finalizados,
                                                COUNT(CASE WHEN p.estatusplano IN (1, 2, 3) THEN 1 END) AS ensambles_pendientes
                                            FROM 
                                                usuarios u
                                            LEFT JOIN 
                                                asignaciondiagrama a ON u.codigo = a.codigooperador
                                            LEFT JOIN 
                                                diagrama p ON a.idplano = p.id
                                            WHERE 
                                                (u.rol = 4 OR u.rol = 13)  AND u.estatus = 1
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
                        $query = "SELECT * FROM usuarios WHERE (rol = 8 OR rol = 4) AND estatus = 1 ORDER BY id DESC";
                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $registro) {
                                $registro_id = $registro['id'];
                        ?>
                                <div class="slickc">
                                    <div class="card" style="width: 100%;border:none;">
                                        <img class="card-img-top" style="object-fit:cover;height:350px;width: 100%;border-radius:5px;" src="<?= $registro['medio']; ?>" alt="Foto perfil">
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

        var finalizados = <?= json_encode($finalizados); ?>;
        var usuarios = <?= json_encode($usuarios); ?>;
        var colors = <?= json_encode($colors); ?>;

        var ctx = document.getElementById('miGrafica').getContext('2d');

        var miGrafica = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: usuarios,
                datasets: [{
                    label: 'Maquinados Finalizados',
                    data: finalizados,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.6', '1')), // Bordes con opacidad al 100%
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
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