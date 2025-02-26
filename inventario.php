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

//Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Inventario' WHERE `usuarios`.`codigo` = '$codigo'";
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
    <title>Inventario | Solara</title>
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
            <div class="container-fluid">
                <div class="row mb-5 mt-5">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>INVENTARIO
                                    <button type="button" class="btn btn-dark btn-sm float-end m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo material
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm float-end  m-1" data-bs-toggle="modal" data-bs-target="#exampleModalTres">
                                        Entrada
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm float-end  m-1" data-bs-toggle="modal" data-bs-target="#exampleModalDos">
                                        Salida
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                            <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
    <thead>
        <tr>
            <th>#</th>
            <th>Material</th>
            <th>Cantidad</th>
            <th>Parte</th>
            <th>Clasificación</th>
            <th>Tipo</th>
            <th>Proveedor</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Condición</th>
            <th>Rack</th>
            <th>Bin</th>
            <th>Caja</th>
            <th>Número</th>
            <th>Costo unitario</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM inventario ORDER BY id DESC";
        $query_run = mysqli_query($con, $query);
        if (mysqli_num_rows($query_run) > 0) {
            $material_partes = [];  // Para almacenar combinaciones de 'nombre', 'parte' y otros campos
            while ($registro = mysqli_fetch_assoc($query_run)) {
                // Combinación de nombre, parte y otros campos
                $clave = implode('|', [
                    $registro['nombre'], 
                    $registro['parte'], 
                    $registro['marca'], 
                    $registro['condicion'], 
                    $registro['clasificacion'], 
                    $registro['bin'], 
                    $registro['tipo'], 
                    $registro['proveedor']
                ]);
                
                // Si ya existe la combinación, marcar como duplicado
                if (isset($material_partes[$clave])) {
                    $material_partes[$clave][] = $registro; // Añadir el registro duplicado completo
                } else {
                    $material_partes[$clave] = [$registro]; // Crear nueva entrada con el registro
                }
        ?>
                <tr>
                    <td><?= $registro['id']; ?></td>
                    <td><?= $registro['nombre']; ?></td>
                    <td><?= $registro['cantidad']; ?></td>
                    <td><?= $registro['parte']; ?></td>
                    <td><?= $registro['clasificacion']; ?></td>
                    <td><?= $registro['tipo']; ?></td>
                    <td><?= $registro['proveedor']; ?></td>
                    <td><?= $registro['descripcion']; ?></td>
                    <td><?= $registro['marca']; ?></td>
                    <td><?= $registro['condicion']; ?></td>
                    <td><?= $registro['rack']; ?></td>
                    <td><?= $registro['bin']; ?></td>
                    <td><?= $registro['caja']; ?></td>
                    <td><?= $registro['numero']; ?></td>
                    <td>$<?= $registro['costo']; ?></td>
                    <td>
                        <a href="editarinventario.php?id=<?= $registro['id']; ?>" class="btn btn-success btn-sm m-1"><i class="bi bi-pencil-square"></i></a>
                        <form action="codeinventario.php" method="POST" class="d-inline">
                            <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='16'><p>No se encontró ningún registro</p></td></tr>";
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO MATERIAL</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeinventario.php" method="POST" class="row">
                        <div class="form-floating col-9">
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            <label for="nombre">Nombre</label>
                        </div>

                        <div class="form-floating col-3">
                            <select class="form-select" name="clasificacion" id="clasificacion" autocomplete="off" required>
                                <option selected disabled>Seleccione una opcion</option>
                                <option value="Controles">Controles</option>
                                <option value="Neumatica">Neumatica</option>
                                <option value="Tooling">Tooling</option>
                                <option value="Mecanico">Mecanico</option>
                            </select>
                            <label for="clasificacion">Clasificación</label>
                        </div>

                        <div class="form-floating col-4 mt-3">
                            <select class="form-select" name="tipo" id="tipo" autocomplete="off" required>
                                <option selected disabled>Seleccione una opcion</option>
                                <option value="Componente">Componente</option>
                                <option value="Consumible">Consumible</option>
                            </select>
                            <label for="tipo">Tipo</label>
                        </div>

                        <div class="form-floating col-4 mt-3">
                            <select class="form-select" name="condicion" id="condicion" autocomplete="off" required>
                                <option selected disabled>Seleccione una opcion</option>
                                <option value="Nuevo">Nuevo</option>
                                <option value="Usado">Usado</option>
                            </select>
                            <label for="condicion">Condición</label>
                        </div>

                        <div class="form-floating col-4 mt-3">
                            <input type="text" class="form-control" name="proveedor" id="proveedor" placeholder="Proveedor" autocomplete="off" required>
                            <label for="proveedor">Propietario</label>
                        </div>

                        <div class="form-floating col-6 mt-3">
                            <input type="text" class="form-control" name="parte" id="parte" placeholder="Parte" autocomplete="off" required>
                            <label for="parte">Parte</label>
                        </div>

                        <div class="form-floating col-4 mt-3">
                            <input type="text" class="form-control" name="marca" id="marca" placeholder="Marca" autocomplete="off" required>
                            <label for="marca">Marca</label>
                        </div>

                        <div class="form-floating col-2 mt-3">
                            <input type="text" class="form-control" name="rack" id="rack" placeholder="Rack" autocomplete="off" required>
                            <label for="rack">Rack</label>
                        </div>

                        <div class="form-floating col-2 mt-3">
                            <input type="text" class="form-control" name="bin" id="bin" placeholder="Bin" autocomplete="off" required>
                            <label for="bin">Bin</label>
                        </div>

                        <div class="form-floating col-3 mt-3">
                            <input type="number" class="form-control" name="cantidad" id="cantidad" placeholder="Cantidad" autocomplete="off" required>
                            <label for="cantidad">Cantidad</label>
                        </div>

                        <div class="form-floating col-5 mt-3" id="maximoContainer" style="display: none;">
                            <input type="number" class="form-control" name="maximo" id="maximo" placeholder="Maximo" autocomplete="off">
                            <label for="maximo">Maximo (Reorden)</label>
                        </div>

                        <div class="form-floating col-7 mt-3" id="minimoContainer" style="display: none;">
                            <input type="number" class="form-control" name="minimo" id="minimo" placeholder="Minimo" autocomplete="off">
                            <label for="minimo">Minimo (Reorden)</label>
                        </div>

                        <div class="form-floating col-2 mt-3">
                            <input type="text" class="form-control" name="caja" id="caja" placeholder="Caja" autocomplete="off" required>
                            <label for="caja">Caja</label>
                        </div>

                        <div class="form-floating col-2 mt-3">
                            <input type="text" class="form-control" name="numero" id="numero" placeholder="Numero" autocomplete="off" required>
                            <label for="numero">Número</label>
                        </div>

                        <div class="form-floating col-3 mt-3">
                            <input type="number" class="form-control" name="costo" id="costo" placeholder="Costo" autocomplete="off" required>
                            <label for="costo">Costo</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción" style="min-height: 120px;"></textarea>
                            <label for="descripcion" class="form-label">Descripción</label>
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

    <!-- Modal 2-->
    <div class="modal fade" id="exampleModalDos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">REGISTRAR SALIDA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeinventario.php" method="POST" class="row">
                        <div class="form-floating col-9 mt-3">
                            <select class="form-select" name="id" id="id">
                                <option disabled selected>Seleccione un material</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM inventario";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($material = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del material
                                        $opcion = $material['nombre'];
                                        // Obtener el ID del usuario
                                        $idMaterial = $material['id'];
                                        $cantidadact = $material['cantidad'];
                                        // Mostrar la opción con el valor igual al ID del material
                                        echo "<option value='$idMaterial' " . ($registro['id'] == $idMaterial ?: '') . ">#$idMaterial $opcion - $cantidadact</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="id">Material saliente</label>
                        </div>

                        <div class="form-floating col-md-3 mt-3 mb-3">
                            <input type="int" class="form-control" name="salida" id="salida" placeholder="Cantidad saliente" autocomplete="off" required>
                            <label for="salida">Cantidad saliente</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="restar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 3-->
    <div class="modal fade" id="exampleModalTres" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">REGISTRAR ENTRADA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeinventario.php" method="POST" class="row">
                        <div class="form-floating col-9 mt-3">
                            <select class="form-select" name="id" id="id">
                                <option disabled selected>Seleccione un material</option>
                                <?php
                                // Consulta a la base de datos para obtener los proyectos
                                $query = "SELECT * FROM inventario";
                                $result = mysqli_query($con, $query);

                                // Verificar si hay resultados
                                if (mysqli_num_rows($result) > 0) {
                                    while ($material = mysqli_fetch_assoc($result)) {
                                        // Construir el texto de la opción con nombre del material
                                        $opcion = $material['nombre'];
                                        $cantidadact = $material['cantidad'];
                                        $parte = $material['parte'];
                                        // Obtener el ID del usuario
                                        $idMaterial = $material['id'];
                                        // Mostrar la opción con el valor igual al ID del material
                                        echo "<option value='$idMaterial' " . ($registro['id'] == $idMaterial ?: '') . ">#$idMaterial $opcion - $parte - $cantidadact</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="id">Material entrante</label>
                        </div>

                        <div class="form-floating col-md-3 mt-3 mb-3">
                            <input type="int" class="form-control" name="entrada" id="entrada" placeholder="Cantidad saliente" autocomplete="off" required>
                            <label for="entrada">Cantidad entrante</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="sumar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const maximoContainer = document.getElementById('maximoContainer');
            const minimoContainer = document.getElementById('minimoContainer');

            tipoSelect.addEventListener('change', function() {
                const selectedOption = tipoSelect.value;

                if (selectedOption === 'Consumible') {
                    maximoContainer.style.display = 'block';
                    minimoContainer.style.display = 'block';
                } else {
                    maximoContainer.style.display = 'none';
                    minimoContainer.style.display = 'none';
                }
            });
        });
        $(document).ready(function() {
            $('#miTabla, #miTablaDos').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "pageLength": 100
            });
        });

// Definir la variable duplicadosList a partir de los datos PHP
const duplicados = <?php echo json_encode($material_partes); ?>;

// Filtrar solo duplicados (más de una ocurrencia)
const duplicadosList = [];
for (const key in duplicados) {
    if (duplicados[key].length > 1) {
        duplicadosList.push(duplicados[key]);
    }
}

let currentIndex = 0;


function mostrarDuplicados(index) {
    if (index < duplicadosList.length) {
        const duplicado = duplicadosList[index];

        let duplicadoHTML = '<p>Selecciona el material que deseas mantener (Se borrara la cantidad de los demas materiales), o fusionalos para sumar la cantidad de todos los materiales duplicados</p><div class="duplicado-container">';
        let sumaCantidad = 0;
        duplicado.forEach(item => {
            sumaCantidad += parseInt(item.cantidad);
            duplicadoHTML += `
                <div class="duplicado-item">
                    <input type="radio" name="selectedId" value="${item.id}" id="item-${item.id}">
                    <label for="item-${item.id}">${item.id} - ${item.nombre} - ${item.cantidad}</label><br>
                </div>
            `;
        });
        duplicadoHTML += '</div>';

        duplicadoHTML += `<input type="number" id="cantidadInput" value="${sumaCantidad}" min="1" class="swal2-input">`;

        Swal.fire({
            title: '¡Alerta de Duplicados!',
            html: duplicadoHTML,
            icon: 'warning',
            width: '1000px',
            showCancelButton: true,
            confirmButtonText: 'Fusionar',
            cancelButtonText: 'Cancelar',
            didOpen: () => { // <-- Función que se ejecuta después de abrir el modal

                const radioButtons = document.querySelectorAll('input[name="selectedId"]');
                const cantidadInput = document.getElementById('cantidadInput');

                radioButtons.forEach(radio => {
                    radio.addEventListener('change', () => {
                        if (radio.checked) {
                            const selectedItem = duplicado.find(item => item.id === radio.value);
                            cantidadInput.value = selectedItem.cantidad; // Actualizar con la cantidad del ID seleccionado
                        } else {
                            cantidadInput.value = sumaCantidad; //Restablecer la suma si se deselecciona
                        }
                    });
                });
            },
            preConfirm: () => {
                return true;
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedRadio = document.querySelector('input[name="selectedId"]:checked');
                const cantidad = document.getElementById('cantidadInput').value;

                if (selectedRadio) {
                    // Caso 1: Se seleccionó un ID
                    const selectedId = selectedRadio.value;
                    const idsToDelete = duplicado.filter(item => item.id !== selectedId).map(item => item.id);
                    const url = `codeinventario.php?updateId=${selectedId}&cantidad=${cantidad}&deleteIds=${idsToDelete.join(',')}`; // Usar updateId
                    window.location.href = url;
                } else {
                    // Caso 2: No se seleccionó un ID (usar el primer ID)
                    const firstId = duplicado[0].id;
                    const idsToDelete = duplicado.slice(1).map(item => item.id); // Eliminar todos menos el primero
                    const url = `codeinventario.php?updateId=${firstId}&cantidad=${cantidad}&deleteIds=${idsToDelete.join(',')}`; // Usar updateId
                    window.location.href = url;
                }
            } else {
                console.log("Proceso detenido por el usuario.");
                currentIndex = duplicadosList.length;
            }

            if (currentIndex < duplicadosList.length && result.isConfirmed) {
                currentIndex++;
                mostrarDuplicados(currentIndex);
            }
        });
    } else {
        console.log("No hay más duplicados para mostrar.");
        currentIndex = 0;
    }
}

if (duplicadosList.length > 0) {
    mostrarDuplicados(currentIndex);
} else {
    console.log("No hay duplicados para mostrar.");
}
    </script>
</body>

</html>