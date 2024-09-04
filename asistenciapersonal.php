<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión
$codigo = $_SESSION['codigo'];
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

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Asistencia personal' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Asistencia personal | Solara</title>
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

                <div class="row justify-content-center mb-4">
                    <div class="col-12">
                        <h3 class="p-2 bg-dark text-light align-items-top" style="text-transform: uppercase;border-radius:5px;">
                            <?php
                            if (isset($_GET['id'])) {
                                $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                $query = "SELECT * FROM usuarios WHERE codigo='$registro_id' AND estatus = 1";
                                $query_run = mysqli_query($con, $query);

                                if (mysqli_num_rows($query_run) > 0) {
                                    $registro = mysqli_fetch_array($query_run);
                                    $nombre = $registro['nombre'];
                                    $apellidop = $registro['apellidop'];
                                    $apellidom = $registro['apellidom'];
                            ?>
                                    <div class="row">
                                        <div class="col-1"><img style="width: 100%;border-radius:5px;height:100px;object-fit: cover;" src="data:image/jpeg;base64,<?php echo base64_encode($registro['medio']); ?>" alt="Foto perfil"></div>
                                        <div class="col-11">
                                            <a href="asistencia.php" class="btn btn-primary btn-sm float-end">Regresar</a>
                                            <b>Asistencia</b><br>
                                            Registro de <b><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></b><br>
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">REGISTRO GENERAL
                                <button id="generalEx" class="btn btn-sm btn-success btn-sm float-end m-1">Excel</button>
                                    <button id="generalPdf" class="btn btn-sm btn-danger btn-sm float-end m-1">PDF</button>
                                </h4>
                            </div>
                            <div class="card-body">
                                <table id="miTabla" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Entrada</th>
                                            <th scope="col">Salida</th>
                                            <th scope="col">Jornada</th>
                                            <th scope="col">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                        $query = "SELECT 
            fecha, 
            MIN(entrada) AS entrada_earliest, 
            MAX(salida) AS salida_latest, 
            SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(salida, entrada)))) AS horas_trabajadas_total 
          FROM 
            asistencia 
          WHERE 
            idcodigo = $registro_id 
          GROUP BY 
            fecha";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            $index = 1;
                                            foreach ($query_run as $registro) {
                                                // Calcular individualmente el tiempo de trabajo para cada registro
                                                $entrada_earliest = $registro['entrada_earliest'];
                                                $salida_latest = $registro['salida_latest'];
                                                $horas_trabajadas_individual = date_diff(date_create($entrada_earliest), date_create($salida_latest))->format('%H:%I');

                                                // Mostrar la hora más temprana de entrada y la hora más tardía de salida
                                                $hora_entrada = $registro['entrada_earliest'];
                                                $hora_salida = $registro['salida_latest'];

                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $index++; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $hora_entrada; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $hora_salida; ?></p>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            <?php if ($hora_salida == NULL) {
                                                                echo '-';
                                                            } else {
                                                                echo "$horas_trabajadas_individual <small>hrs</small>";
                                                            } ?>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['fecha']; ?></p>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="text-transform: uppercase;">REGISTRO DETALLADO
                                <button id="detalladoEx" class="btn btn-sm btn-success btn-sm float-end m-1">Excel</button>
                                    <button id="detalladoPdf" class="btn btn-sm btn-danger btn-sm float-end m-1">PDF</button>
                                </h4>
                            </div>
                            <div class="card-body">
                                <table id="miTablaDos" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Entrada</th>
                                            <th scope="col">Salida</th>
                                            <th scope="col">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                        $fecha_actual = date('Y-m-d'); // Obtiene la fecha actual en formato 'YYYY-MM-DD'

                                        $query = "SELECT * FROM asistencia WHERE idcodigo = $registro_id";
                                        $query_run = mysqli_query($con, $query);

                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td>
                                                        <p><?= $registro['id']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['entrada']; ?></p>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($registro['fecha'] < $fecha_actual && $registro['salida'] === NULL) {
                                                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                                                        ?>
                                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $registro['id']; ?>">Completar</button>
                                                                <!-- Modal para solicitud de salida -->
                                                                <div class="modal fade" id="exampleModal<?= $registro['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?= $registro['id']; ?>" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h1 class="modal-title fs-5" id="exampleModalLabel<?= $registro['id']; ?>">ACTUALIZAR HORA DE SALIDA</h1>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form id="asistenciaForm<?= $registro['id']; ?>" action="codeasistencia.php" method="POST" class="row">
                                                                                    <input type="hidden" id="id<?= $registro['id']; ?>" name="id" value="<?= $registro['id']; ?>">
                                                                                    <input type="hidden" id="codigo<?= $registro['id']; ?>" name="codigo" value="<?= $registro['idcodigo']; ?>">
                                                                                    <div class="form-floating col-12 mb-3">
                                                                                        <input type="text" class="form-control" id="fechadetail<?= $registro['id']; ?>" value="<?= $registro['fecha']; ?>" placeholder="Fecha" autocomplete="off" disabled>
                                                                                        <label for="fechadetail<?= $registro['id']; ?>">Fecha <span class="small">(YYYY/MM/DD)</span></label>
                                                                                    </div>
                                                                                    <div class="form-floating col-12 mb-3">
                                                                                        <input type="time" class="form-control" id="entradadetail<?= $registro['id']; ?>" value="<?= $registro['entrada']; ?>" placeholder="Entrada" autocomplete="off" disabled>
                                                                                        <label for="entradadetail<?= $registro['id']; ?>">Hora de entrada</label>
                                                                                    </div>
                                                                                    <div class="form-floating col-12 mb-3">
                                                                                        <input style="background-color:#ffffff;" type="time" class="form-control" name="salidadetail" id="salidadetail<?= $registro['id']; ?>" placeholder="Salida" autocomplete="off" required>
                                                                                        <label for="salidadetail<?= $registro['id']; ?>">Ingresa la hora de salida</label>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <p id="duracionJornada<?= $registro['id']; ?>">La jornada será de: </p>
                                                                                    </div>

                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                                        <?php if ($registro['idcodigo'] == $codigo) { ?>
                                                                                            <button type="submit" class="btn btn-primary" name="propio">Actualizar</button>
                                                                                        <?php } else { ?>
                                                                                            <button type="submit" class="btn btn-primary" name="solicitar">Solicitar</button>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                        <?php
                                                            }
                                                        } else {
                                                            if ($registro['estatus'] == 0) {
                                                                echo "<p>" . $registro['salida'] . "</p>";
                                                            } else {
                                                                echo 'En revisión';
                                                            }
                                                        }


                                                        ?>
                                                    </td>
                                                    <td>
                                                        <p><?= $registro['fecha']; ?></p>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'><p>No se encontró ningún registro</p></td></tr>";
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.4/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.15/jspdf.plugin.autotable.js"></script>
        <script>
            $(document).ready(function() {
                $('#miTabla, #miTablaDos').DataTable({
                    "order": [
                        [0, "desc"]
                    ],
                    "pageLength": 25
                });


                <?php foreach ($query_run as $registro) { ?>
                    $('#exampleModal<?= $registro['id']; ?>').on('shown.bs.modal', function() {
                        $("#salidadetail<?= $registro['id']; ?>").on("input", function() {
                            calcularDuracionJornada("<?= $registro['id']; ?>");
                        });
                    });

                    function calcularDuracionJornada(registroId) {
                        var entrada = $("#entradadetail" + registroId).val();
                        var salida = $("#salidadetail" + registroId).val();

                        if (entrada && salida) {
                            var entradaHora = new Date("2000-01-01 " + entrada);
                            var salidaHora = new Date("2000-01-01 " + salida);
                            var duracionMs = salidaHora - entradaHora;

                            var duracionHoras = Math.floor(duracionMs / (1000 * 60 * 60));
                            var duracionMinutos = Math.floor((duracionMs % (1000 * 60 * 60)) / (1000 * 60));

                            $("#duracionJornada" + registroId).html("La jornada será de: " + duracionHoras + " horas y " + duracionMinutos + " minutos.");
                        }
                    }
                <?php } ?>

                $('#generalEx').click(function() {
                    // Crear una nueva hoja de cálculo
                    var wb = XLSX.utils.book_new();
                    // Convertir la tabla de datos a un formato Excel
                    var ws = XLSX.utils.table_to_sheet($('#miTabla')[0]);
                    // Agregar la hoja de cálculo al libro de trabajo
                    XLSX.utils.book_append_sheet(wb, ws, "Registros");
                    // Generar el archivo Excel
                    var wbout = XLSX.write(wb, {
                        bookType: 'xlsx',
                        type: 'binary'
                    });

                    // Convertir el archivo Excel a un blob
                    var blob = new Blob([s2ab(wbout)], {
                        type: 'application/octet-stream'
                    });
                    // Crear un objeto URL para el blob
                    var url = URL.createObjectURL(blob);

                    // Crear un enlace para descargar el archivo Excel
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = 'registros.xlsx';
                    // Simular un clic en el enlace para iniciar la descarga
                    link.click();

                    // Liberar el objeto URL
                    URL.revokeObjectURL(url);
                });

                $('#detalladoEx').click(function() {
                    // Clonar la tabla para conservar la original
                    var $tableClone = $('#miTablaDos').clone();
                    // Eliminar los modales del clon
                    $tableClone.find('.modal').remove();

                    // Crear una nueva hoja de cálculo
                    var wb = XLSX.utils.book_new();
                    // Convertir solo la tabla clonada a un formato Excel
                    var ws = XLSX.utils.table_to_sheet($tableClone[0]);
                    // Agregar la hoja de cálculo al libro de trabajo
                    XLSX.utils.book_append_sheet(wb, ws, "Registros Detallados");
                    // Generar el archivo Excel
                    var wbout = XLSX.write(wb, {
                        bookType: 'xlsx',
                        type: 'binary'
                    });

                    // Convertir el archivo Excel a un Blob
                    var blob = new Blob([s2ab(wbout)], {
                        type: 'application/octet-stream'
                    });

                    // Crear un objeto URL para el blob
                    var url = URL.createObjectURL(blob);

                    // Crear un enlace para descargar el archivo Excel
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = 'registros_detallados.xlsx';
                    // Simular un clic en el enlace para iniciar la descarga
                    link.click();

                    // Liberar el objeto URL
                    URL.revokeObjectURL(url);
                });


                // Función auxiliar para convertir una cadena en una matriz de bytes
                function s2ab(s) {
                    var buf = new ArrayBuffer(s.length);
                    var view = new Uint8Array(buf);
                    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                    return buf;
                }

                window.jsPDF = window.jspdf.jsPDF;

                $('#generalPdf').click(function() {
                    var doc = new jsPDF();
                    doc.autoTable({
                        html: '#miTabla'
                    });
                    doc.save('registros_general.pdf');
                });

                $('#detalladoPdf').click(function() {
                    // Clonar la tabla para conservar la original
                    var $tableClone = $('#miTablaDos').clone();
                    // Eliminar los modales del clon
                    $tableClone.find('.modal').remove();

                    var doc = new jsPDF();
                    // Solo convertir la tabla clonada a PDF
                    doc.autoTable({
                        html: $tableClone[0]
                    });
                    doc.save('registros_detallados.pdf');
                });
            });
        </script>
</body>

</html>