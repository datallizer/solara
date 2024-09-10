<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';


// Verificar si hay una sesión activa y si los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Monitor de sesiones' WHERE `usuarios`.`codigo` = '$codigo'";
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

// Lógica para manejar las actualizaciones de las sesiones en tiempo real
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_sessions') {
    $response = array();

    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i");

    $query = "SELECT u.nombre, u.apellidop, u.apellidom, u.rol, u.estatus, u.ubicacion,
          (CASE WHEN EXISTS (
              SELECT 1 
              FROM asistencia a 
              WHERE a.idcodigo = u.codigo 
              AND DATE(a.fecha) = '$fecha_actual' 
              AND TIME(a.fecha) <= '$hora_actual' 
              AND a.salida IS NULL
              AND u.ubicacion <> 'Fin de jornada laboral'
          ) THEN '1' ELSE '0' END) AS activo
          FROM usuarios u
          WHERE u.estatus = 1 AND (u.rol = 8 OR u.rol = 4)
          ORDER BY u.id DESC";


    $result = mysqli_query($con, $query);

    if (!$result) {
        // Manejo de errores en caso de fallo en la consulta
        echo "Error en la consulta: " . mysqli_error($con);
        exit(); // Finalizar el script en caso de error
    }

    if (mysqli_num_rows($result) > 0) {
        $response['sessions'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $response['sessions'] = array();
    }

    echo json_encode($response); // Imprimir el resultado de la consulta para verificar si hay datos
    exit(); // Finalizar el script después de devolver la respuesta JSON
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
    <title>Monitor | Solara</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row justify-content-evenly mt-1 mb-1">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>ACTIVIDAD DE OPERADORES EN TIEMPO REAL <a style="color:#000;padding:3px 7px;border-radius:5px;" class="btn btn-warning float-end" href="logout.php" role="button">
                                <i class="bi bi-box-arrow-right"></i> Salir
                            </a></h4>
                    </div>
                    <div class="card-body" style="overflow-y:scroll;">
                        <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;font-size:20px;">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Ubicación</th>
                                    <th>Sesión</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row" style="color: #fff;">
            <div class="col m-1 text-center bg-primary" style="border-radius: 10px;padding:15px 5px;">
                <p><b>MAQUINADOS<br>PENDIENTES</b></p>
                <p style="font-size: 45px;" id="total_asignados"></p>
            </div>

            <div class="col-2 m-1 text-center bg-primary" style="border-radius: 10px;padding:15px 5px;">
                <p><b>MAQUINADOS EN<br>PROGRESO</b></p>
                <p style="font-size: 45px;" id="total_progreso"></p>
            </div>

            <div class="col-2 m-1 text-center bg-primary" style="border-radius: 10px;padding:15px 5px;">
                <p><b>MAQUINADOS<br>PAUSADOS</b></p>
                <p style="font-size: 45px;" id="total_pausados"></p>
            </div>

            <div class="col-2 m-1 text-center bg-success" style="border-radius: 10px;padding:15px 5px;">
                <p><b>ENSAMBLES<br>PENDIENTES</b></p>
                <p style="font-size: 45px;" id="ensambles_asignados"></p>
            </div>

            <div class="col-2 m-1 text-center bg-success" style="border-radius: 10px;padding:15px 5px;">
                <p><b>ENSAMBLES EN<br>PROGRESO</b></p>
                <p style="font-size: 45px;" id="ensambles_progreso"></p>
            </div>

            <div class="col-2 m-1 text-center bg-success" style="border-radius: 10px;padding:15px 5px;">
                <p><b>ENSAMBLES<br>PAUSADOS</b></p>
                <p style="font-size: 45px;" id="ensambles_pausados"></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-3">
                <table id="miTablaTres" class="table table-bordered table-striped" style="width: 100%;">
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
        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script> -->
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Activar modo pantalla completa',
                //text: 'Debes activar el modo pantalla completa para continuar.',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.setItem('fullscreenAccepted', 'true');
                    requestFullscreen();
                }
            });

        });

        function requestFullscreen() {
            let elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) { // Firefox
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) { // Chrome, Safari and Opera
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { // IE/Edge
                elem.msRequestFullscreen();
            }
        }
        $(document).ready(function() {
            function getSessions() {
                $.ajax({
                    url: 'sesiones.php?action=get_sessions',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#miTabla tbody').empty();
                        $('#miTablaDos tbody').empty();

                        response.sessions.forEach(function(session) {
                            // Filtrar solo los roles 4 y 8
                            if (session.rol === '4' || session.rol === '8') {
                                var sessionStatus = '<i style="color:#09e83a;" class="bi bi-circle-fill"></i> Activo';
                                var sessionRow = '<tr>' +
                                    '<td>' + session.nombre + ' ' + session.apellidop + ' ' + session.apellidom + '</td>' +
                                    '<td>' + getRoleName(session.rol) + '</td>' +
                                    '<td>' + session.ubicacion + '</td>' +
                                    '<td>' + sessionStatus + '</td>' +
                                    '</tr>';

                                if (session.activo === '1') {
                                    $('#miTabla tbody').append(sessionRow);
                                } else {
                                    $('#miTablaDos tbody').append(sessionRow);
                                }
                            }
                        });
                    }
                });
            }

            getSessions();

            // Inicializar DataTables para las tablas una vez que los datos estén presentes
            // $('#miTabla').DataTable();
            // $('#miTablaDos').DataTable();

            setInterval(getSessions, 1000);

            function getRoleName($roleId) {
                switch ($roleId) {
                    case '1':
                        return "Administrador";
                    case '2':
                        return "Gerencia";
                    case '4':
                        return "Técnico controles";
                    case '5':
                        return "Ing. Diseño";
                    case '6':
                        return "Compras";
                    case '7':
                        return "Almacenista";
                    case '8':
                        return "Técnico mecanico";
                    case '9':
                        return "Ing. Control";
                    case '10':
                        return "Recursos humanos";
                    default:
                        return "Error, contacte a soporte";
                }
            }

            function updateTotals() {
                $.ajax({
                    url: 'get_totals.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#total_asignados').text(data.asignados);
                        $('#total_progreso').text(data.progreso);
                        $('#total_pausados').text(data.pausados);
                    },
                    error: function() {
                        console.error("No se pudieron obtener los datos.");
                    }
                });
            }

            // Llamar a la función para actualizar los datos cada 5 segundos
            setInterval(updateTotals, 1000);

            // Llamar la primera vez al cargar la página
            updateTotals();

            function updateEnsambles() {
                $.ajax({
                    url: 'get_totalsensambles.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#ensambles_asignados').text(data.asignados);
                        $('#ensambles_progreso').text(data.progreso);
                        $('#ensambles_pausados').text(data.pausados);
                    },
                    error: function() {
                        console.error("No se pudieron obtener los datos.");
                    }
                });
            }

            // Llamar a la función para actualizar los datos cada 5 segundos
            setInterval(updateEnsambles, 1000);

            // Llamar la primera vez al cargar la página
            updateEnsambles();

            function actualizarTabla() {
                $.ajax({
                    url: 'obtener_datos.php', // Ruta al archivo PHP que obtiene los datos
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tabla = $('#miTablaTres tbody');
                        tabla.empty(); // Limpia el contenido actual de la tabla

                        $.each(data, function(index, registro) {
                            // Calcula el porcentaje de progreso
                            var porcentaje = 0;
                            if (registro.suma_piezas > 0) {
                                porcentaje = (registro.suma_piezas_terminadas / registro.suma_piezas) * 100;
                            }

                            var tr = $('<tr class="text-center">').append(
                                $('<td>').text(registro.id),
                                $('<td>').text(registro.nombre),
                                $('<td>').css('background-color', getColorForPrioridad(registro.prioridad)).text(registro.prioridad),
                                $('<td>').text(registro.suma_piezas),
                                $('<td>').text(registro.suma_asignadas),
                                $('<td>').text(registro.suma_piezas_terminadas),
                                $('<td>').html(
                                    $('<div class="progreso">').css({
                                        'width': porcentaje + '%',
                                        'background-color': '#829bd1',
                                        'position': 'relative'
                                    }).append(
                                        $('<span>').text(porcentaje.toFixed(2) + '%').css('color', '#000000')
                                    )
                                )
                            );

                            tabla.append(tr);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al obtener los datos: ', status, error);
                    }
                });
            }

            // Función para obtener el color de fondo según la prioridad
            function getColorForPrioridad(prioridad) {
                var colors = [
                    '#ff0000', '#ff1a1a', '#ff3333', '#ff4d4d', '#ff6666', '#ff8080', '#ff9999',
                    '#ffb2b2', '#ffcccc', '#ffe5e5', '#ffffb3', '#ffff99', '#ffff80', '#ffff66',
                    '#ffff4d', '#ffff33', '#ffff1a', '#ffff00', '#ffff00', '#e5e500', '#c6e500',
                    '#a8e500', '#89e500', '#67e500', '#58e500', '#39e500', '#26e500', '#00e500',
                    '#00e51b', '#00e539'
                ];
                return colors[prioridad - 1] || '#ffffff'; // Devuelve el color correspondiente
            }

            // Actualiza la tabla cada 5 segundos
            setInterval(actualizarTabla, 1000);


            actualizarTabla();

        });
    </script>
</body>

</html>