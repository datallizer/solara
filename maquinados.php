<?php
session_start();
require 'dbcon.php';

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
// if (isset($_SESSION['username'])) {
//     $username = $_SESSION['username'];

//     // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
//     $query = "SELECT * FROM usuarios WHERE username = '$username'";
//     $result = mysqli_query($con, $query);

//     // Si se encuentra un registro coincidente, el usuario está autorizado
//     if (mysqli_num_rows($result) > 0) {
//         // El usuario está autorizado, se puede acceder al contenido
//     } else {
//         // Redirigir al usuario a una página de inicio de sesión
//         header('Location: login.php');
//         exit(); // Finalizar el script después de la redirección
//     }
// } else {
//     // Redirigir al usuario a una página de inicio de sesión si no hay una sesión activa
//     header('Location: login.php');
//     exit(); // Finalizar el script después de la redirección
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Maquinados | Solara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>MAQUINADOS
                                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo plano
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <?php include('message.php'); ?>
                                <table class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Proyecto</th>
                                            <th>Planos asociados</th>
                                            <th>Operadores asignados</th>
                                            <th>Número de piezas</th>
                                            <th>Nivel de pieza</th>
                                            <th>Etapa</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT proyecto.*, plano.*
                                        FROM plano 
                                        JOIN proyecto ON plano.idproyecto = proyecto.id 
                                        ORDER BY plano.id DESC";
                                        $query_run = mysqli_query($con, $query);
                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $registro) {
                                        ?>
                                                <tr>
                                                    <td><?= $registro['nombre']; ?></td>
                                                    <td><?= $registro['nombreplano']; ?></td>
                                                    <td><?= $registro['medio']; ?></td>
                                                    <td><?= $registro['piezas']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($registro['nivel'] === '1') {
                                                            echo "Nivel 1";
                                                        } else if ($registro['nivel'] === '2') {
                                                            echo "Nivel 2";
                                                        } else if ($registro['nivel'] === '3') {
                                                            echo "Nivel 3";
                                                        } else if ($registro['nivel'] === '4') {
                                                            echo "Nivel 4";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($registro['etapa'] === '1') {
                                                            echo "Diseño";
                                                        } else if ($registro['etapa'] === '2') {
                                                            echo "Revisión interna";
                                                        } else if ($registro['etapa'] === '3') {
                                                            echo "Revisión con cliente";
                                                        } else if ($registro['etapa'] === '4') {
                                                            echo "Planos";
                                                        } else if ($registro['etapa'] === '5') {
                                                            echo "Bom";
                                                        } else if ($registro['etapa'] === '6') {
                                                            echo "Remediación";
                                                        } else if ($registro['etapa'] === '7') {
                                                            echo "Documentación";
                                                        } else {
                                                            echo "Error, contacte a soporte";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="editarusuario.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                            <form action="codeusuarios.php" method="POST" class="d-inline">
                                                                <button type="submit" name="delete" value="' . $registro['id'] . '" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                            </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<td><p>No se encontro ningun registro</p></td><td></td><td></td><td></td><td></td><td></td>";
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
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PLANO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codemaquinados.php" method="POST" class="row">
                        <div class="form-floating col-12 mb-3">
                            <select class="form-select" name="idproyecto" id="idproyecto">
                                <option disabled selected>Seleccione un proyecto</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM proyecto WHERE estatus = 1";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($proyecto = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del proyecto
                                        $opcion = $proyecto['nombre'];
                                        // Obtener el ID del usuario
                                        $idProyecto = $proyecto['id'];
                                        // Mostrar la opción con el valor igual al ID del proyecto
                                        echo "<option value='$idProyecto' " . ($registro['id'] == $idProyecto ? 'selected' : '') . ">$opcion</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="idproyecto">Proyecto asociado</label>
                        </div>

                        <div class="form-floating col-12 mt-1">
                            <input type="text" class="form-control" name="nombreplano" id="nombreplano" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombreplano">Nombre del plano</label>
                        </div>

                        <div class="mt-3">
                            <label for="formFile" class="form-label">Plano PDF</label>
                            <input class="form-control" type="file" id="formFile" name="medio">
                        </div>


                        <div class="form-floating col-12 col-md-5 mt-3">
                            <input type="text" class="form-control" name="piezas" id="piezas" placeholder="Piezas" autocomplete="off" required>
                            <label for="piezas">Número de piezas</label>
                        </div>

                        <div class="form-floating col-12 col-md-7 mt-3">
                            <select class="form-select" name="nivel" id="nivel" autocomplete="off" required>
                                <option selected disabled>Seleccione el nivel</option>
                                <option value="1">Nivel 1</option>
                                <option value="2">Nivel 2</option>
                                <option value="3">Nivel 3</option>
                                <option value="4">Nivel 4</option>
                            </select>
                            <label for="nivel">Nivel de pieza</label>
                        </div>

                        <div class="form-floating col-12 mt-3 mt-3">
                            <select class="form-select" name="etapa" id="etapa" autocomplete="off" required>
                                <option selected disabled>Seleccione la etapa</option>
                                <option value="1">Diseño</option>
                                <option value="2">Revisión interna</option>
                                <option value="3">Revisión con cliente</option>
                                <option value="4">Planos</option>
                                <option value="5">Bom</option>
                                <option value="6">Remediación</option>
                                <option value="7">Documentación</option>
                            </select>
                            <label for="etapa">Etapa:</label>
                        </div>

                        <div class="form-check col-12 mt-3 m-3">
                            <?php
                            // Consulta a la base de datos para obtener los usuarios con rol igual a 8
                            $query = "SELECT * FROM usuarios WHERE rol = 8";
                            $result = mysqli_query($con, $query);

                            // Verificar si hay resultados
                            if (mysqli_num_rows($result) > 0) {
                                while ($usuario = mysqli_fetch_assoc($result)) {
                                    $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                    $idUsuario = $usuario['codigo'];
                                    
                                    // Cambio en el nombre del campo para que se envíen como un array
                                    echo "<input class='form-check-input' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                    echo "<label class='form-check-label' for='codigooperador_$idUsuario'>$nombreCompleto</label><br>";
                                }
                            }
                            ?>
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
</body>

</html>