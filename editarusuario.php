<?php
session_start();
require 'dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Usuarios | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
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
                                <h4>EDITAR USUARIO
                                    <a href="usuarios.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php
                                
                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM usuarios WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $rol_actual = $registro['rol'];
                                        $estatus_actual = $registro['estatus'];

                                ?>
                                
                                        <form action="codeusuarios.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                            <div class="form-group mt-3 col-11 mb-3">
                                                <label for="nuevaFoto">Seleccionar nueva foto:</label>
                                                <input type="file" class="form-control" id="nuevaFoto" name="nuevaFoto">
                                            </div>
                                            <div class="form-group mb-3 col-1 text-center">
                                                <?php
                                                // Mostrar la imagen actual si existe
                                                if (!empty($registro['medio'])) {
                                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($registro['medio']) . '" alt="Foto actual" style="width:100%;">';
                                                } else {
                                                    echo 'No hay foto actual.';
                                                }
                                                ?>
                                            </div>

                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>">
                                                    <label for="nombre">Nombre</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="apellidop" id="apellidop" value="<?= $registro['apellidop']; ?>">
                                                    <label for="apellidop">Apellido Paterno</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="apellidom" id="apellidom" value="<?= $registro['apellidom']; ?>">
                                                    <label for="apellidom">Apellido materno</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <input type="text" class="form-control" name="codigo" id="codigo" value="<?= $registro['codigo']; ?>" readonly>
                                                    <label for="codigo">Codigo de acceso</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-7 mt-3">
                                                <select class="form-select" name="estatus" id="estatus">
                                                    <option disabled>Seleccione un estatus</option>
                                                    <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                    <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                </select>
                                                    <label for="estatus">Estatus</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-5 mt-3">
                                                <select class="form-select" name="rol" id="rol">
                                                    <option disabled>Seleccione un rol</option>
                                                    <option value="1" <?= ($rol_actual == 1) ? 'selected' : ''; ?>>Administrador</option>
                                                    <option value="2" <?= ($rol_actual == 2) ? 'selected' : ''; ?>>Gerencia</option>
                                                    <option value="3" <?= ($rol_actual == 3) ? 'selected' : ''; ?>>Tecnico mecanico</option>
                                                    <option value="4" <?= ($rol_actual == 4) ? 'selected' : ''; ?>>Tecnico controles</option>
                                                    <option value="5" <?= ($rol_actual == 5) ? 'selected' : ''; ?>>Ing- Diseño</option>
                                                    <option value="6" <?= ($rol_actual == 6) ? 'selected' : ''; ?>>Compras</option>
                                                    <option value="7" <?= ($rol_actual == 7) ? 'selected' : ''; ?>>Almacenista</option>
                                                    <option value="8" <?= ($rol_actual == 8) ? 'selected' : ''; ?>>Operador</option>
                                                    <option value="9" <?= ($rol_actual == 9) ? 'selected' : ''; ?>>Ing.Control</option>
                                                </select>
                                                    <label for="rol">Rol</label>
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar usuario
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