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

//Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Configuracion SMTP
$host = 'smtp.gmail.com';
$port = 587;
$username = 'solarasystemai@gmail.com';
$password = 'owwd pbtr bpfh brff';
$security = 'tls';

$query = "SELECT * FROM inventario WHERE tipo = 'Consumible'";
$query_run = mysqli_query($con, $query);

while ($registro = mysqli_fetch_assoc($query_run)) {
    // Obtener valores
    $cantidad = $registro['cantidad'];
    $minimo = $registro['minimo'];
    $maximo = $registro['maximo'];

    // Evaluar si la cantidad es menor o igual al mínimo y reorden es igual a "1"
    if ($cantidad <= $minimo && $registro['reorden'] == 1) {

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
        $mail->addAddress('storage@solara-industries.com');
        $mail->Subject = 'Reorden: Inventario de ' . $registro['id'] . ' ' . $registro['nombre'] . ' bajo en stock';
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        // Cuerpo del mensaje
        $body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body>
            <img style="width:100%;" src="https://datallizer.com/images/solarasuperior.jpg" alt="">
            <h1 style="font-size:25px;margin-top:30px;text-align:left;margin-bottom:30px;"><b>REORDEN</b></h1>
            <p>El producto id ' . $registro['id'] . ' ' . $registro['nombre'] . ' tiene un stock bajo.</p>
            <p><b>Cantidad actual:</b> ' . $cantidad . '</p>
            <p><b>Minimo recomendado:</b> ' . $minimo . '</p>
            <p><b>Maximo recomendado:</b> ' . $maximo . '</p>

            <p style="margin-top:30px;"><b>Detalles</b></p>
            <p><b>Proveedor:</b> ' . $registro['proveedor'] . '</p>
            <p><b>Parte:</b> ' . $registro['parte'] . '</p>
            <p><b>Marca:</b> ' . $registro['marca'] . '</p>
            <p><b>Condición:</b> ' . $registro['condicion'] . '</p>
            <p><b>Costo:</b> $' . $registro['costo'] . '</p>

            <p style="font-size:10px;">Este es un email enviado automaticamente por el sistema de planificación de recursos empresariales SOLARA AI, la información previa a sido generada utilizando datos históricos almacenados en la base de datos de SOLARA, es importante tener en cuenta que las cantidades, materiales u otros detalles presentados en esta propuesta podrían estar desactualizados, descontinuados o contener errores. Le recomendamos verificar la precisión de la información presentada antes de tomar decisiones basadas en estos datos desde los submódulos "Inventario" y "Reorden".</p>
            </body>
            </html>
            ';
        $mail->Body = $body;

        // Enviar correo
        if ($mail->send()) {
            mysqli_query($con, "UPDATE inventario SET reorden = 0 WHERE id = " . $registro['id']);
        } else {
            $_SESSION['message'] = "Error al solicitar reorden";
        }
    } elseif ($cantidad > $minimo && $registro['reorden'] == 0) {
        mysqli_query($con, "UPDATE inventario SET reorden = 1 WHERE id = " . $registro['id']);
    }
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
    <title>Dashboard | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-md-center justify-content-start mt-5 mb-3">
                    <div class="col-12">
                        <h2 class="mb-3">PERMISOS</h2>
                    </div>
                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="#"><i class="bi bi-arrow-counterclockwise" style="font-size: 30px;"></i><br>Historico</a>
                    </div>
                    <div class="col card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="#"><i class="bi bi-x-circle-fill" style="font-size: 30px;"></i><br>Permisios rechazados</a>
                    </div>
                    <?php
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                        echo '
                    <div class="col-3 card text-center m-1 bg-dark">
                        <a style="color: #fff;" class="p-3" href="asistencia.php"><i class="bi bi-clipboard2-check-fill" style="font-size: 30px;"></i><br>Asistencia</a>
                    </div>';
                    }
                    ?>
                </div>
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>SOLICITUDES VIGENTES
                                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nueva solicitud
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <?php include('message.php'); ?>
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Usuario</th>
                                            <th>Detalle</th>
                                            <th>Rol</th>
                                            <th>Fecha / Tiempo</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM permisos ORDER BY id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></td>
                                                    <td><?= $registro['codigo']; ?></td>
                                                    <td>
                                                        <?php
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
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($registro['estatus'] === '0') {
                                                            echo "Inactivo";
                                                        } else if ($registro['estatus'] === '1') {
                                                            echo "Activo";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="editarusuario.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <?php
                                                        if ($registro['id'] != 1) {
                                                            echo '
                                                            <form action="codeusuarios.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="' . $registro['id'] . '" class="btn btn-danger btn-sm m-1 deletebtn"><i class="bi bi-trash-fill"></i></button>
                                                            </form>';
                                                        }
                                                        ?>

                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td colspan='6'><p>No se encontro ningun registro</p></td>";
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO USUARIO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeusuarios.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre</label>
                        </div>

                        <div class="form-floating col-md-6 mt-3">
                            <input type="text" class="form-control" name="apellidop" id="apellidop" placeholder="Apellido paterno" autocomplete="off" required>
                            <label for="apellidop">Apellido paterno</label>
                        </div>

                        <div class="form-floating col-12 col-md-6 mt-3">
                            <input type="text" class="form-control" name="apellidom" id="apellidom" placeholder="Apellido materno" autocomplete="off" required>
                            <label for="apellidom">Apellido Materno</label>
                        </div>

                        <div class="form-floating col-12 mt-3">
                            <input type="int" class="form-control" name="codigo" id="codigo" placeholder="Codigo acceso" autocomplete="off" required>
                            <label for="codigo">Codigo acceso</label>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="medio" class="form-label">Foto de perfil</label>
                            <input type="file" class="form-control" name="medio" id="medio" required>
                        </div>

                        <div class="form-floating col-12 mt-3 mb-3">
                            <select class="form-select" name="rol" id="rol" autocomplete="off" required>
                                <option selected disabled>Seleccione el rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Gerencia</option>
                                <option value="4">Técnico controles</option>
                                <option value="5">Ing. Diseño</option>
                                <option value="6">Compras</option>
                                <option value="7">Almacenista</option>
                                <option value="8">Técnico mecanico</option>
                                <option value="9">Ing. Control</option>
                                <option value="10">Recursos humanos</option>
                            </select>
                            <label for="rol">Rol</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25
            });
        });

        const deleteButtons = document.querySelectorAll('.deletebtn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                const id = e.target.value; // Obtener el valor del botón delete

                // Mostrar la alerta de SweetAlert2 para confirmar la eliminación
                Swal.fire({
                    title: '¿Estás seguro que deseas eliminar este registro?',
                    text: '¡No podrás deshacer esta acción!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('delete', id);

                        fetch('codeusuarios.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);

                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>