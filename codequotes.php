<?php
require 'dbcon.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    // Depuración: Mostrar el ID de la cita antes de ejecutar la consulta de eliminación
    echo "ID de la cita a eliminar: " . $registro_id;

    $query = "DELETE FROM quotes WHERE id='$registro_id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Quote eliminado exitosamente";
        header("Location: quotes.php");
        exit(0);
    } else {
        // Mostrar cualquier error de MySQL que pueda ocurrir
        echo mysqli_error($con);
        $_SESSION['message'] = "Error al eliminar el quote, contácte a soporte";
        header("Location: quotes.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $notas = mysqli_real_escape_string($con, $_POST['notas']);

    $query = "UPDATE `quotes` SET `notas` = '$notas' WHERE `quotes`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Quote editado exitosamente";
        header("Location: quotes.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el quote, contacte a soporte";
        header("Location: quotes.php");
        exit(0);
    }
}

if (isset($_POST['updatemonto'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $monto = mysqli_real_escape_string($con, $_POST['monto']);
    $pasado = mysqli_real_escape_string($con, $_POST['pasado']);
    $cotizacion = mysqli_real_escape_string($con, $_POST['cotizacion']);

    $query = "UPDATE `quotes` SET `monto` = '$monto' WHERE `quotes`.`id` = '$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $idcodigo = $_SESSION['codigo'];
        $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
        $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos

        $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Actualizo el monto de la compra finalizada $cotizacion de $$pasado a $$monto', hora='$hora_actual', fecha='$fecha_actual'";
        $query_rundos = mysqli_query($con, $querydos);
        $_SESSION['message'] = "Monto de compra editado exitosamente";
        header("Location: compras.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al editar el monto de la compra, contacte a soporte";
        header("Location: compras.php");
        exit(0);
    }
}


if (isset($_POST['aprobar'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['aprobar']);

    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';

    // Configuracion SMTP
    $host = 'smtp.gmail.com';
    $port = 587;
    $username = 'solarasystemai@gmail.com';
    $password = 'owwd pbtr bpfh brff';
    $security = 'tls';
    // Obtener los datos de la fila aprobada
    $query = "SELECT * FROM quotes WHERE id = '$registro_id'";
    $query_run = mysqli_query($con, $query);
    $registro_aprobado = mysqli_fetch_assoc($query_run);

    // Construir el contenido HTML del correo electrónico
    $html_content = "
        <html>
        <head>
            <title>Quote Aprobado</title>
        </head>
        <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #000;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }
</style>
        <body>
        <img style='width:100%;' src='https://datallizer.com/images/solarasuperior.jpg' alt=''>
            <h2>DETALLES DEL QUOTE APROBADO</h2>
            <table style='margin-top:30px; margin-bottom:80px;'>
                <tr>
                    <th>Id</th>
                    <th>Solicitante</th>
                    <th>Rol</th>
                    <th>Proyecto</th>
                    <th> Ver PDF</th>
                    <th>Notas</th>
                    <th>Estatus</th>
                </tr>
                <tr>
                    <td>{$registro_aprobado['id']}</td>
                    <td>{$registro_aprobado['solicitante']}</td>
                    <td>";
                    if ($registro_aprobado['rol'] === '1') {
                        $html_content .= "Administrador";
                    } else if ($registro_aprobado['rol'] === '2') {
                        $html_content .= "Gerencia";
                    } else if ($registro_aprobado['rol'] === '4') {
                        $html_content .= "Técnico controles";
                    } else if ($registro_aprobado['rol'] === '5') {
                        $html_content .= "Ing. Diseño";
                    } else if ($registro_aprobado['rol'] === '6') {
                        $html_content .= "Compras";
                    } else if ($registro_aprobado['rol'] === '7') {
                        $html_content .= "Almacenista";
                    } else if ($registro_aprobado['rol'] === '8') {
                        $html_content .= "Técnico mecanico";
                    } else if ($registro_aprobado['rol'] === '9') {
                        $html_content .= "Ing. Control";
                    } else {
                        $html_content .= "Error, contacte a soporte";
                    }
                    $html_content .= "</td>
                    <td>{$registro_aprobado['cotizacion']}</td>
                    <td><a href='http://192.168.1.38:81/solara/vercotizacion.php?id={$registro_aprobado['id']}'>Ver PDF</a></td>
                    <td>{$registro_aprobado['notas']}</td>
                    <td>Aprobado</td>
                </tr>
            </table>

            <p style='font-size:10px;'>Este es un email enviado automáticamente por el sistema de planificación de recursos empresariales SOLARA AI, la información previa ha sido generada utilizando datos históricos almacenados en la base de datos de SOLARA, es importante tener en cuenta que la información u otros detalles presentados en este email podrían estar desactualizados, descontinuados o contener errores. Le recomendamos verificar la precisión de la información presentada antes de tomar decisiones basadas en estos datos desde el submódulo 'Compras'</p>
        </body>
        </html>
    ";


    // Crear instancia PHPMailer
    $mail = new PHPMailer(true);

    // Configurar SMTP
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = $security;
    //$mail->SMTPDebug = 2;



    // Configurar correo
    $mail->setFrom('solarasystemai@gmail.com', 'SOLARA AI');
    $mail->addAddress('purchasing@solara-industries.com');
    $mail->Subject = 'Compra aprobada id ' . $registro_aprobado['id'] . ' ' . $registro_aprobado['cotizacion'];
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Body = $html_content;

    // Enviar correo electrónico
    if ($mail->send()) {
        // Actualizar el estado del quote a 'aprobado'
        $query_update = "UPDATE `quotes` SET `estatusq` = '0' WHERE `quotes`.`id` = '$registro_id'";
        $query_run_update = mysqli_query($con, $query_update);

        if ($query_run_update) {
            $_SESSION['message'] = "Quote aprobado exitosamente, se envió la notificación a compras";
            header("Location: quotes.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al aprobar el quote, contacte a soporte";
            header("Location: quotes.php");
            exit(0);
        }
    } else {
        $_SESSION['message'] = "Error al enviar la notificación a compras";
        header("Location: quotes.php");
        exit(0);
    }
} elseif (isset($_POST['completar'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['completar']);
    $monto = mysqli_real_escape_string($con, $_POST['monto']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);

    $query = "UPDATE `quotes` SET `monto` = '$monto', `estatusq` = '$estatus' WHERE `quotes`.`id` = '$registro_id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Compra actualizada exitosamente";
        header("Location: compras.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al actualizar el quote, contacte a soporte";
        header("Location: compras.php");
        exit(0);
    }
}



if (isset($_POST['save'])) {
    $solicitante = mysqli_real_escape_string($con, $_POST['solicitante']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $proyecto = mysqli_real_escape_string($con, $_POST['proyecto']);
    $cotizacion = mysqli_real_escape_string($con, $_POST['cotizacion']);
    $medio = addslashes(file_get_contents($_FILES['medio']['tmp_name']));
    $notas = mysqli_real_escape_string($con, $_POST['notas']);

    $query = "INSERT INTO quotes SET solicitante='$solicitante', rol='$rol', proyecto='$proyecto', cotizacion='$cotizacion', estatusq='1', medio='$medio', notas='$notas'";

    $query_run = mysqli_query($con, $query);
    if ($query_run) {
        $_SESSION['message'] = "Quote creado exitosamente";
        header("Location: quotes.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al crear el quote, contacte a soporte";
        header("Location: quotes.php");
        exit(0);
    }
}
?>