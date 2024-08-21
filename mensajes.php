<?php
require 'dbcon.php';
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$codigo = isset($_SESSION['codigo']) ? $_SESSION['codigo'] : '';

// Manejar la solicitud AJAX para marcar como leído
if (isset($_POST['action']) && $_POST['action'] == 'leido') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $estatus = '0';

    $query = "UPDATE `mensajes` SET `estatus` = '$estatus' WHERE `mensajes`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// Manejar la solicitud AJAX para verificar nuevos mensajes
if (isset($_POST['action']) && $_POST['action'] == 'checkNewMessage') {
    $sql = "SELECT * FROM mensajes WHERE estatus = 1 AND idcodigo = '$codigo' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $registro = mysqli_fetch_assoc($result);
        $data = [
            'id' => $registro['id'],
            'mensaje' => $registro['mensaje'],
            'emisor' => $registro['emisor'],
            'hora' => $registro['hora'],
            'fecha' => $registro['fecha']
        ];
        echo json_encode($data);
    } else {
        echo json_encode(['status' => 'no_message']);
    }
    exit;
}

// Código para mostrar el modal y demás contenido HTML si se carga la página
$sql = "SELECT * FROM mensajes WHERE estatus = 1 AND idcodigo = '$codigo' LIMIT 1";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $registro = mysqli_fetch_assoc($result);
    $id = $registro['id'];
    $mensaje = $registro['mensaje'];
    $emisor = $registro['emisor'];
    $hora = $registro['hora'];
    $fecha = $registro['fecha'];
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mensaje').modal('show');
        });
    </script>
<?php
}
?>


<!-- Modal solicitud salida -->
<div class="modal fade" id="mensaje" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><b>NUEVO MENSAJE DE <?= $registro['emisor']; ?></b></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="leidoForm" class="row">
                    <input type="hidden" name="id" value="<?= $id; ?>">
                    <div class="form-floating col-12 mb-3">
                        <textarea type="text" class="form-control" placeholder="Mensaje" disabled style="min-height: 180px;"><?= $mensaje; ?></textarea>
                        <label for="entrada">Mensaje</label>
                    </div>
                    <div class="col-12">
                        <p class="small">Recibido el <?= $fecha; ?> a las <?= $hora; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="leidoBtn">Marcar leído</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Función para verificar nuevos mensajes
        function checkNewMessage() {
            $.ajax({
                type: 'POST',
                url: 'mensajes.php',
                data: { action: 'checkNewMessage' },
                dataType: 'json',
                success: function(response){
                    if(response.status !== 'no_message'){
                        // Mostrar el modal con el nuevo mensaje
                        $('#mensaje').find('.modal-title').html('<b>NUEVO MENSAJE DE ' + response.emisor + '</b>');
                        $('#mensaje').find('textarea').val(response.mensaje);
                        $('#mensaje').find('p.small').text('Recibido el ' + response.fecha + ' a las ' + response.hora);
                        $('input[name="id"]').val(response.id);
                        $('#mensaje').modal('show');
                    }
                },
                error: function(){
                    console.log('Error en la solicitud AJAX para verificar nuevos mensajes.');
                }
            });
        }

        // Verificar nuevos mensajes cada 5 segundos (5000 milisegundos)
        setInterval(checkNewMessage, 10000);

        // Manejar el botón de "Marcar leído"
        $('#leidoBtn').click(function(){
            var formData = $('#leidoForm').serialize(); // Serializar los datos del formulario

            $.ajax({
                type: 'POST',
                url: 'mensajes.php',
                data: formData + '&action=leido',
                success: function(response){
                    console.log(response); // Verifica la respuesta en la consola
                    if(response.trim() == 'success'){
                        $('#mensaje').modal('hide'); // Cerrar el modal
                    } else {
                        alert('Ocurrió un error al intentar marcar el mensaje como leído.');
                    }
                },
                error: function(){
                    alert('Ocurrió un error al intentar marcar el mensaje como leído.');
                }
            });
        });
    });
</script>
