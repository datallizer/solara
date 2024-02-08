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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar material | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png" />
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container mt-5">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>EDITAR MATERIAL
                                    <a href="inventario.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php
                                
                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM inventario WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $tipo_actual = $registro['tipo'];
                                        $clasificacion_actual = $registro['clasificacion'];
                                        $condicion_actual = $registro['condicion'];

                                ?>
                                
                                        <form action="codeinventario.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>">
                                                    <label for="nombre">Nombre</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                <select class="form-select" name="clasificacion" id="clasificacion">
                                                    <option disabled>Seleccione una opcion</option>
                                                    <option value="Controles" <?= ($clasificacion_actual == 'Controles') ? 'selected' : ''; ?>>Controles</option>
                                                    <option value="Neumatica" <?= ($clasificacion_actual == 'Neumatica') ? 'selected' : ''; ?>>Neumatica</option>
                                                    <option value="Tooling" <?= ($clasificacion_actual == 'Tooling') ? 'selected' : ''; ?>>Tooling</option>
                                                    <option value="Mecanico" <?= ($clasificacion_actual == 'Mecanico') ? 'selected' : ''; ?>>Mecanico</option>
                                                </select>
                                                    <label for="clasificacion">Clasificacion</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                <select class="form-select" name="tipo" id="tipo">
                                                    <option disabled>Seleccione una opcion</option>
                                                    <option value="Componente" <?= ($tipo_actual == 'Componente') ? 'selected' : ''; ?>>Componente</option>
                                                    <option value="Consumible" <?= ($tipo_actual == 'Consumible') ? 'selected' : ''; ?>>Consumible</option>
                                                </select>
                                                    <label for="tipo">Tipo</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                <select class="form-select" name="condicion" id="condicion">
                                                    <option disabled>Seleccione una opcion</option>
                                                    <option value="Nuevo" <?= ($condicion_actual == 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                                                    <option value="Usado" <?= ($condicion_actual == 'Usado') ? 'selected' : ''; ?>>Usado</option>
                                                </select>
                                                    <label for="condicion">Condicion</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-5 mt-3">
                                                    <input type="text" class="form-control" name="proveedor" id="proveedor" value="<?= $registro['proveedor']; ?>">
                                                    <label for="proveedor">Proveedor</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-4 mt-3">
                                                    <input type="text" class="form-control" name="parte" id="parte" value="<?= $registro['parte']; ?>">
                                                    <label for="parte">Parte</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-3 mt-3">
                                                    <input type="text" class="form-control" name="bin" id="bin" value="<?= $registro['bin']; ?>">
                                                    <label for="bin">Bin</label>
                                                </div>

                                                <div class="form-floating col-6 mt-3">
                                                    <input type="text" class="form-control" name="marca" id="marca" value="<?= $registro['marca']; ?>">
                                                    <label for="marca">Marca</label>
                                                </div>

                                                <div class="form-floating col-4 mt-3">
                                                    <input type="text" class="form-control" name="rack" id="rack" value="<?= $registro['rack']; ?>">
                                                    <label for="rack">Rack</label>
                                                </div>

                                                <div class="form-floating col-2 mt-3">
                                                    <input type="text" class="form-control" name="cantidad" id="cantidad" value="<?= $registro['cantidad']; ?>">
                                                    <label for="cantidad">Cantidad</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                    <input type="text" class="form-control" name="marca" id="marca" value="<?= $registro['marca']; ?>">
                                                    <label for="marca">Marca</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                    <input type="text" class="form-control" name="caja" id="caja" value="<?= $registro['caja']; ?>">
                                                    <label for="caja">Caja</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                    <input type="text" class="form-control" name="numero" id="numero" value="<?= $registro['numero']; ?>">
                                                    <label for="numero">Número</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                    <input type="text" class="form-control" name="costo" id="costo" value="<?= $registro['costo']; ?>">
                                                    <label for="costo">Costo</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <textarea type="text" class="form-control" name="descripcion" id="descripcion" style="min-height: 150px;"><?= $registro['descripcion']; ?></textarea>
                                                    <label for="descripcion">Descripcion</label>
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar
                                                    </button>
                                                </div>


                                            </div>
                            </div>

                            </form>
                    <?php
                                    } else {
                                        echo "<h4>No Such Id Found</h4>";
                                    }
                                }
                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>