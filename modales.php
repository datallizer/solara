<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$codigo = $_SESSION['codigo'];

$sql = "SELECT id,estatus, idcodigo, entrada, salida, fecha FROM asistencia WHERE estatus = 1 AND idcodigo = '$codigo'";
$result = mysqli_query($con, $sql);

// Revision para hora de salida ----------------------------------------------------------------
if (mysqli_num_rows($result) > 0) {
    $registro = mysqli_fetch_assoc($result);
    $entrada = $registro['entrada'];
    $salida = $registro['salida'];
    $fecha = $registro['fecha'];
    $idregistro = $registro['id'];
    $idcodigo = $registro['idcodigo'];

    // Convertir la hora de entrada y salida a objetos DateTime
    $entrada_dt = new DateTime($entrada);
    $salida_dt = new DateTime($salida);

    // Calcular la diferencia entre la hora de entrada y salida
    $duracion_jornada = $entrada_dt->diff($salida_dt)->format('%H:%I'); // Formato horas:minutos
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#revision').modal('show');
        });
    </script>
    <?php
}

// ETAPA 2 Consulta para verificar si el proyecto está en la etapa de visita --------------------------------
$sqlagenda = "SELECT proyecto.id, proyecto.nombre, proyecto.etapa, proyecto.estatus
              FROM proyecto
              JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
              WHERE (proyecto.etapa = 2 OR proyecto.etapa = 8)
              AND (proyecto.estatus = 2 OR proyecto.estatus = 1)
              AND encargadoproyecto.codigooperador = $codigo;";
$resultagenda = mysqli_query($con, $sqlagenda);

// Si se encuentra un proyecto
if (mysqli_num_rows($resultagenda) > 0) {
    $registro = mysqli_fetch_assoc($resultagenda);
    $idregistroproyecto = $registro['id'];
    $nombreproyecto = $registro['nombre'];
    $etapa = $registro['etapa'];

    // Verificamos si ya existe un registro con estatus = 1 en agendaproyectos para este proyecto
    $sqlcheck = "SELECT id FROM agendaproyectos WHERE idproyecto = $idregistroproyecto AND estatus = 1;";
    $resultcheck = mysqli_query($con, $sqlcheck);

    // Si no existe un registro con estatus = 1, mostramos el modal
    if (mysqli_num_rows($resultcheck) == 0) {
    ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#agendaproyecto').modal('show');
            });
        </script>
    <?php
    }
}


// ETAPA 2 Minuta modal ----------------------------------------------------------------
$fecha_actual = date("Y-m-d");
$hora_actual = date("H:i");
$sqlminuta = "SELECT ap.*, p.nombre
FROM agendaproyectos ap
JOIN proyecto p ON p.id = ap.idproyecto
WHERE ap.idcodigo = $codigo
  AND (ap.dia < '$fecha_actual'
  OR (ap.dia = '$fecha_actual' AND ap.hora <= '$hora_actual')) 
  AND ap.estatus = 1 
  AND (ap.etapa = 2 OR ap.etapa = 8);";

$resultminuta = mysqli_query($con, $sqlminuta);

if (mysqli_num_rows($resultminuta) > 0) {
    $registro = mysqli_fetch_assoc($resultminuta);
    $etapaagenda = $registro['etapa'];
    $dia = $registro['dia'];
    $hora = $registro['hora'];
    $idregistroagenda = $registro['id'];
    $idcodigoagenda = $registro['idcodigo'];
    $nombreagenda = $registro['nombre'];
    $idproyectoagenda = $registro['idproyecto'];

    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#minuta').modal('show');
        });
    </script>
<?php
}
// ETAPA 3 Documento modal SQL
$sqldocumento = "SELECT proyecto.id, proyecto.nombre, proyecto.etapa, proyecto.estatus
              FROM proyecto
              JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
              WHERE (proyecto.etapa = 3 OR proyecto.etapa = 9)
              AND (proyecto.estatus = 1 OR proyecto.estatus = 2)
              AND encargadoproyecto.codigooperador = $codigo
              AND proyecto.id NOT IN (
                  SELECT idproyecto 
                  FROM proyectomedios 
                  WHERE (estatus = 1 OR estatus = 3) AND idcodigo = $codigo);";

$resultdocumento = mysqli_query($con, $sqldocumento);

if (mysqli_num_rows($resultdocumento) > 0) {
    $registro = mysqli_fetch_assoc($resultdocumento);
    $idregistrodocumento = $registro['id'];
    $nombredocumento = $registro['nombre'];
    $etapadocumento = $registro['etapa'];
    $estatusdocumento = $registro['estatus'];

    $sqlcheck = "SELECT id, detalles FROM proyectomedios WHERE idproyecto = $idregistrodocumento AND estatus = 2 AND idcodigo = $codigo;";
    $resultcheck = mysqli_query($con, $sqlcheck);

    if (mysqli_num_rows($resultcheck) == 0) {
        // No hay registro con estatus = 1
        $detallesdocumento = '';
    } else {
        // Existe un registro con estatus = 2
        $detalleRegistro = mysqli_fetch_assoc($resultcheck);
        $detallesdocumento = $detalleRegistro['detalles'];
    }

?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#documentos').modal('show');
        });
    </script>
<?php
}

// ETAPA 4 Generacion de BOMs
$sqldocumento = "SELECT proyecto.id, proyecto.nombre, proyecto.etapa, proyecto.estatus
              FROM proyecto
              JOIN encargadoproyecto ON proyecto.id = encargadoproyecto.idproyecto
              WHERE (proyecto.etapa = 4 OR proyecto.etapa = 11)
              AND (proyecto.estatus = 1 OR proyecto.estatus = 2)
              AND encargadoproyecto.codigooperador = $codigo
              AND proyecto.id NOT IN (
                  SELECT idproyecto 
                  FROM proyectoboms 
                  WHERE (estatus = 1 OR estatus = 3) AND idcodigo = $codigo);";

$resultdocumento = mysqli_query($con, $sqldocumento);

if (mysqli_num_rows($resultdocumento) > 0) {
    $registro = mysqli_fetch_assoc($resultdocumento);
    $idregistrodocumento = $registro['id'];
    $nombredocumento = $registro['nombre'];
    $etapadocumento = $registro['etapa'];
    $estatusdocumento = $registro['estatus'];

    $sqlcheck = "SELECT id, detalles FROM proyectoboms WHERE idproyecto = $idregistrodocumento AND estatus = 2 AND idcodigo = $codigo;";
    $resultcheck = mysqli_query($con, $sqlcheck);

    if (mysqli_num_rows($resultcheck) == 0) {
        // No hay registro con estatus = 1
        $detallesdocumento = '';
    } else {
        // Existe un registro con estatus = 2
        $detalleRegistro = mysqli_fetch_assoc($resultcheck);
        $detallesdocumento = $detalleRegistro['detalles'];
    }

?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#boms').modal('show');
        });
    </script>
<?php
}
?>
<style>
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
    }

    .spinner-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .spinner {
        width: 3rem;
        height: 3rem;
    }
</style>
<!-- Modal solicitud salida -->
<div class="modal fade" id="revision" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b>COMPLETAR HORA DE SALIDA</b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeasistencia.php" method="POST" class="row">
                    <input type="hidden" id="id" name="id" value="<?= $idregistro; ?>">
                    <input type="hidden" id="codigo" name="codigo" value="<?= $idcodigo; ?>">
                    <div class="col-12">
                        <p class="small">Recibiste una solicitud de revisión sobre tu <b>hora de salida</b>, verifica que los datos sean correctos y si estas de acuerdo aprueba la solicitud.</p>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="text" class="form-control" id="fecha" value="<?= $fecha; ?>" placeholder="Fecha" disabled>
                        <label for="fecha">Fecha <span class="small">(YYYY/MM/DD)</span></label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input type="text" class="form-control" id="entrada" value="<?= $entrada; ?>" placeholder="Entrada" disabled>
                        <label for="entrada">Hora de entrada</label>
                    </div>
                    <div class="form-floating col-6 mb-3">
                        <input style="background-color:#ffdca1;" type="text" class="form-control" id="salida" value="<?= $salida; ?>" placeholder="Salida" disabled>
                        <label for="salida">Hora de salida</label>
                    </div>
                    <div class="col-12">
                        <p>Tu jornada fue de: <b><?= $duracion_jornada; ?></b> <span class="small">hrs</span></p>
                    </div>
                    <div class="modal-footer">
                        <p class="small">Tu jornada total de trabajo se calcula con el número de entradas y salidas que registres en un día, si deseas conocer el total de horas trabajadas para este día puedes consultarlo en <a href="asistenciapersonal.php?id=<?= $_SESSION['codigo']; ?>">asistencia</a>.</p>
                        <button type="submit" class="btn btn-danger" name="rechazar">Rechazar</button>
                        <button type="submit" class="btn btn-success" name="aprobar">Aprobar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ETAPA 2 Modal visita de levantamiento con cliente -->
<div class="modal fade" id="agendaproyecto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b><?= $etapa == 2 ? "VISITA DE LEVANTAMIENTO CON CLIENTE" : ($etapa == 8 ? "VISITA FORMAL DE LEVANTAMIENTO" : "Etapa no definida"); ?>
                    </b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeproyecto.php" method="POST" class="row">
                    <input type="hidden" name="idproyecto" value="<?= $idregistroproyecto; ?>">
                    <input type="hidden" name="etapa" value="<?= $etapa; ?>">
                    <div class="col-12">
                        <p class="small">Te encuentras en la etapa <?= $etapa == 2 ? "'Visita de levantamiento con cliente'" : ($etapa == 8 ? "'Visita formal de levantamiento'" : "Etapa no definida"); ?>
                            para el <?= $etapa == 2 ? "anteproyecto" : ($etapa == 8 ? "proyecto" : "ERROR AL VALLIDAR SI SE TRATA DE UN PROYECTO O ANTEPROYECTO"); ?>
                            <b><?= $nombreproyecto; ?></b>, a continuación selecciona el día y hora que tienes planeado realizar la visita:
                        </p>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="date" class="form-control" name="dia" id="dia" placeholder="Día" required>
                        <label for="dia">Fecha de visita</label>
                    </div>
                    <div class="form-floating col-12 mb-3" style="min-height: 150px;">
                        <input type="time" class="form-control" name="hora" id="hora" placeholder="Hora" required>
                        <label for="hora">Hora de visita</label>
                    </div>
                    <div class="modal-footer">
                        <p class="small">Una vez finalices la visita SOLARA AI te notificará para continuar a la siguiente etapa, o podrás actualizarla manualmente</p>
                        <button type="submit" class="btn btn-success" name="agendalevantamiento">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ETAPA 2 Modal minuta -->
<div class="modal fade" id="minuta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b>MINUTA <?= $etapaagenda == 2 ? "VISITA DE LEVANTAMIENTO CON CLIENTE" : ($etapaagenda == 8 ? "VISITA FORMAL DE LEVANTAMIENTO" : "ERROR, NOTIFIQUE A SOPORTE"); ?></b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeproyecto.php" method="POST" class="row">
                    <input type="hidden" name="idproyectoagenda" value="<?= $idproyectoagenda; ?>">
                    <input type="hidden" id="idregistroagenda" name="idregistroagenda" value="<?= $idregistroagenda; ?>">
                    <input type="hidden" id="idcodigoagenda" name="idcodigoagenda" value="<?= $idcodigoagenda; ?>">
                    <div class="col-12">
                        <p class="small">Estas por concluir la etapa <?= $etapaagenda; ?> del <?= $etapaagenda == 2 ? "anteproyecto" : ($etapaagenda == 8 ? "proyecto" : "ERROR, NOTIFIQUE A SOPORTE"); ?> <b class="bg-warning"><?= $nombreagenda; ?></b>, para continuar ingresa los detalles de tu visita, estos se enviarán automaticamente a administración y una copia a tu correo institucional.</p>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="text" class="form-control" name="asunto" id="asunto" placeholder="Asunto" value="<?= $etapaagenda == 2 ? "Visita de levantamiento con cliente" : ($etapaagenda == 8 ? "Visita formal de levantamiento" : "Etapa no definida"); ?>, <?= $etapaagenda == 2 ? "anteproyecto" : ($etapaagenda == 8 ? "proyecto" : "ERROR, NOTIFIQUE A SOPORTE"); ?> <?= $nombreagenda; ?>">
                        <label for="asunto">Asunto</label>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <textarea type="text" class="form-control" name="detalles" id="detalles" placeholder="Detalles" style="min-height: 200px;"></textarea>
                        <label for="fecha">Detalles</label>
                    </div>
                    <div class="col-12">
                        <p class="small"><b>Consideraciones:</b><br> -Una vez enviado el correo no es posible editar la información, asegurate que todo este correcto. <br>-Cuando finalices este paso SOLARA AI actualizara automaticamente a la siguiente etapa.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="minuta">Enviar correo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ETAPA 3 Modal carga documentos -->
<div class="modal fade" id="documentos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b><?= $etapadocumento == 3 ? "CARGA DEL DISEÑO/DIAGRAMA A BLOQUES GENERADO" : ($etapadocumento == 9 ? "CARGA DEL PREDISEÑO (MECÁNICO/ELECTRICO)" : "ERROR"); ?></b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeproyecto.php" method="POST" class="row" enctype="multipart/form-data">
                    <input type="hidden" name="idproyecto" value="<?= $idregistrodocumento; ?>">
                    <input type="hidden" name="etapa" value="<?= $etapadocumento; ?>">
                    <div class="col-12">
                        <p class="small">Te encuentras en la etapa <?= $etapadocumento == 3 ? "3, 'Carga del diseño/diagrama a bloques'" : ($etapadocumento == 9 ? "9, 'Carga del prediseño (Mecánico/electrico)'" : "Error"); ?>
                            para el <?= $etapadocumento == 3 ? "anteproyecto" : ($etapadocumento == 9 ? "proyecto" : "ERROR AL VALIDAR SI SE TRATA DE UN PROYECTO O ANTEPROYECTO"); ?>
                            <b class="bg-warning"><?= $nombredocumento; ?></b>, a continuación carga el documento PDF:
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <input type="file" class="form-control" name="medio" id="medio" placeholder="Documento" accept="application/pdf" required>
                    </div>
                    <!-- Si existe un registro con estatus = 2, mostrar textarea -->
                    <?php if (!empty($detallesdocumento)) { ?>
                        <div class="col-12 mb-3">
                            <label for="detalles" class="form-label bg-danger p-1" style="border-radius: 5px;color:#fff;margin-left: 0px !important;">Estatus: Rechazado</label>
                            <textarea class="form-control" id="detalles" rows="7" readonly>Detalles: <?= htmlspecialchars($detallesdocumento); ?></textarea>
                        </div>
                    <?php } ?>
                    <div class="modal-footer mt-5">
                        <p class="small">Una vez envíes el documento deberá ser aprobado por un administrador para continuar a la siguiente etapa: <?= $etapadocumento == 3 ? "''Generación de BOM's''" : ($etapadocumento == 9 ? "''Revision de diseño/aprobacion de cliente''" : "ERROR AL DETERMINAR LA SIGUIENTE ETAPA"); ?>, de ser rechazado recibirás una notificación con los detalles para la corrección del archivo.</p>
                        <button type="submit" class="btn btn-success" name="documento">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ETAPA 4 Modal carga BOMs -->
<div class="modal fade" id="boms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b><?= $etapadocumento == 4 ? "GENERACIÓN DE BOM" : ($etapadocumento == 11 ? "ACTUALIZACIÓN DE BOM´s" : "ERROR"); ?></b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="codeproyecto.php" method="POST" class="row" enctype="multipart/form-data">
                    <input type="hidden" name="idproyecto" value="<?= $idregistrodocumento; ?>">
                    <input type="hidden" name="etapa" value="<?= $etapadocumento; ?>">
                    <div class="col-12">
                        <p class="small">Te encuentras en la etapa <?= $etapadocumento == 4 ? "4, ''Generación de BOM''" : ($etapadocumento == 11 ? "11, 'Actualización de BOM'" : "Error"); ?>
                            para el <?= $etapadocumento == 4 ? "anteproyecto" : ($etapadocumento == 11 ? "proyecto" : "ERROR AL VALIDAR SI SE TRATA DE UN PROYECTO O ANTEPROYECTO"); ?>
                            <b class="bg-warning"><?= $nombredocumento; ?></b>, a continuación carga el documento PDF eh ingresa el monto total del BOM en USD:
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <input type="file" class="form-control" name="medio" id="medio" placeholder="Documento" accept="application/pdf" required>
                    </div>
                    <div class="form-floating col-12 mb-3">
                        <input type="text" class="form-control" name="monto" id="monto" placeholder="Monto">
                        <label for="monto">Monto total (USD)</label>
                        <span class="small">Ingresa el valor sin incluir el carácter $, esta permitido utilizar puntos y coma.</span>
                    </div>
                    <!-- Si existe un registro con estatus = 2, mostrar textarea -->
                    <?php if (!empty($detallesdocumento)) { ?>
                        <div class="col-12 mb-3">
                            <label for="detalles" class="form-label bg-danger p-1" style="border-radius: 5px;color:#fff;margin-left: 0px !important;">Estatus: Rechazado</label>
                            <textarea class="form-control" id="detalles" rows="7" readonly>Detalles: <?= htmlspecialchars($detallesdocumento); ?></textarea>
                        </div>
                    <?php } ?>
                    <div class="modal-footer mt-5">
                        <p class="small">Una vez envíes el documento deberá ser aprobado por un administrador para continuar a la siguiente etapa: <?= $etapadocumento == 4 ? "''Cotización''" : ($etapadocumento == 11 ? "''Colocación de PO''" : "ERROR AL DETERMINAR LA SIGUIENTE ETAPA"); ?>, de ser rechazado recibirás una notificación con los detalles para la corrección del archivo.</p>
                        <button type="submit" class="btn btn-success" name="bom">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="spinner-overlay" style="z-index: 99999999999999999999999999999999999999999999999999999999999999999999;">
    <div class="spinner-container">
        <div class="spinner-grow text-primary spinner" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();

            $('.spinner-overlay').show(); // Muestra el spinner

            var buttonName = $(this).find('button[type=submit]:focus').attr('name');

            var form = this;
            $('<input>').attr({
                type: 'hidden',
                name: buttonName
            }).appendTo(form);

            // Retrasar el envío del formulario por 10 segundos
            setTimeout(function() {
                form.submit(); // Envía el formulario después de 10 segundos
            }, 10000);
        });
    });
</script>