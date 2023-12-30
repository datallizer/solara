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
    <title>Editar proyecto | Solara</title>
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
                                <h4>EDITAR PROYECTO
                                    <a href="proyectos.php" class="btn btn-danger btn-sm float-end">Regresar</a>
                                </h4>
                            </div>
                            <div class="card-body">

                                <?php
                                
                                if (isset($_GET['id'])) {
                                    $registro_id = mysqli_real_escape_string($con, $_GET['id']);
                                    $query = "SELECT * FROM proyecto WHERE id='$registro_id' ";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        $registro = mysqli_fetch_array($query_run);
                                        $prioridad_actual = $registro['prioridad'];
                                        $diseño_actual = $registro['etapadiseño'];
                                        $control_actual = $registro['etapacontrol'];
                                        $tcontrol_actual = $registro['etapatcontrol'];
                                        $mecanica_actual = $registro['etapamecanica'];
                                        $estatus_actual = $registro['estatus'];

                                ?>
                                
                                        <form action="codeproyecto.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $registro['id']; ?>">

                                            <div class="row mt-1">
                                                <div class="form-floating col-12">
                                                    <input type="text" class="form-control" name="nombre" id="nombre" value="<?= $registro['nombre']; ?>">
                                                    <label for="nombre">Nombre del proyecto</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="cliente" id="cliente" value="<?= $registro['cliente']; ?>" readonly>
                                                    <label for="cliente">Cliente</label>
                                                </div>

                                                <div class="form-floating col-12 col-md-6 mt-3">
                                                    <input type="text" class="form-control" name="presupuesto" id="presupuesto" value="<?= $registro['presupuesto']; ?>">
                                                    <label for="presupuesto">Presupuesto</label>
                                                </div>

                                                <div class="form-floating col-7 mt-3">
                                                    <input type="date" class="form-control" name="fechainicio" id="fechainicio" value="<?= $registro['fechainicio']; ?>" readonly>
                                                    <label for="fechainicio">Fecha de inicio</label>
                                                </div>

                                                <div class="form-floating col-5 mt-3">
                                                    <input type="date" class="form-control" name="fechafin" id="fechafin" value="<?= $registro['fechafin']; ?>">
                                                    <label for="fechafin">Fecha de finalización</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                <select class="form-select" name="estatus" id="estatus">
                                                    <option disabled>Seleccione un estatus</option>
                                                    <option value="0" <?= ($estatus_actual == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                                    <option value="1" <?= ($estatus_actual == 1) ? 'selected' : ''; ?>>Activo</option>
                                                </select>
                                                    <label for="estatus">Estatus</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                <select class="form-select" name="prioridad" id="prioridad">
                                                    <option disabled>Seleccione la prioridad</option>
                                                    <option value="1" <?= ($prioridad_actual == 1) ? 'selected' : ''; ?>>Nivel 1</option>
                                                    <option value="2" <?= ($prioridad_actual == 2) ? 'selected' : ''; ?>>Nivel 2</option>
                                                    <option value="3" <?= ($prioridad_actual == 3) ? 'selected' : ''; ?>>Nivel 3</option>
                                                    <option value="4" <?= ($prioridad_actual == 4) ? 'selected' : ''; ?>>Nivel 4</option>
                                                    <option value="5" <?= ($prioridad_actual == 5) ? 'selected' : ''; ?>>Nivel 5</option>
                                                    <option value="6" <?= ($prioridad_actual == 6) ? 'selected' : ''; ?>>Nivel 6</option>
                                                    <option value="7" <?= ($prioridad_actual == 7) ? 'selected' : ''; ?>>Nivel 7</option>
                                                    <option value="8" <?= ($prioridad_actual == 8) ? 'selected' : ''; ?>>Nivel 8</option>
                                                    <option value="9" <?= ($prioridad_actual == 9) ? 'selected' : ''; ?>>Nivel 9</option>
                                                    <option value="10" <?= ($prioridad_actual == 10) ? 'selected' : ''; ?>>Nivel 10</option>
                                                    <option value="11" <?= ($prioridad_actual == 11) ? 'selected' : ''; ?>>Nivel 11</option>
                                                    <option value="12" <?= ($prioridad_actual == 12) ? 'selected' : ''; ?>>Nivel 12</option>
                                                    <option value="13" <?= ($prioridad_actual == 13) ? 'selected' : ''; ?>>Nivel 13</option>
                                                    <option value="14" <?= ($prioridad_actual == 14) ? 'selected' : ''; ?>>Nivel 14</option>
                                                    <option value="15" <?= ($prioridad_actual == 15) ? 'selected' : ''; ?>>Nivel 15</option>
                                                    <option value="16" <?= ($prioridad_actual == 16) ? 'selected' : ''; ?>>Nivel 16</option>
                                                    <option value="17" <?= ($prioridad_actual == 17) ? 'selected' : ''; ?>>Nivel 17</option>
                                                    <option value="18" <?= ($prioridad_actual == 18) ? 'selected' : ''; ?>>Nivel 18</option>
                                                    <option value="19" <?= ($prioridad_actual == 19) ? 'selected' : ''; ?>>Nivel 19</option>
                                                    <option value="20" <?= ($prioridad_actual == 20) ? 'selected' : ''; ?>>Nivel 20</option>
                                                    <option value="21" <?= ($prioridad_actual == 21) ? 'selected' : ''; ?>>Nivel 21</option>
                                                    <option value="22" <?= ($prioridad_actual == 22) ? 'selected' : ''; ?>>Nivel 22</option>
                                                    <option value="23" <?= ($prioridad_actual == 23) ? 'selected' : ''; ?>>Nivel 23</option>
                                                    <option value="24" <?= ($prioridad_actual == 24) ? 'selected' : ''; ?>>Nivel 24</option>
                                                    <option value="25" <?= ($prioridad_actual == 25) ? 'selected' : ''; ?>>Nivel 25</option>
                                                    <option value="26" <?= ($prioridad_actual == 26) ? 'selected' : ''; ?>>Nivel 26</option>
                                                    <option value="27" <?= ($prioridad_actual == 27) ? 'selected' : ''; ?>>Nivel 27</option>
                                                    <option value="28" <?= ($prioridad_actual == 28) ? 'selected' : ''; ?>>Nivel 28</option>
                                                    <option value="29" <?= ($prioridad_actual == 29) ? 'selected' : ''; ?>>Nivel 29</option>
                                                    <option value="30" <?= ($prioridad_actual == 30) ? 'selected' : ''; ?>>Nivel 30</option>
                                                </select>
                                                    <label for="prioridad">Prioridad</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                <select class="form-select" name="etapadiseño" id="etapadiseño">
                                                    <option disabled>Seleccione la etapa</option>
                                                    <option value="1" <?= ($diseño_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                    <option value="2" <?= ($diseño_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                    <option value="3" <?= ($diseño_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                    <option value="4" <?= ($diseño_actual == 4) ? 'selected' : ''; ?>>Planos</option>
                                                    <option value="5" <?= ($diseño_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                    <option value="6" <?= ($diseño_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                    <option value="7" <?= ($diseño_actual == 7) ? 'selected' : ''; ?>>Remediación</option>
                                                    <option value="8" <?= ($diseño_actual == 8) ? 'selected' : ''; ?>>Documentación</option>
                                                </select>
                                                    <label for="etapadiseño">Etapa de diseño</label>
                                                </div>

                                                <div class="form-floating col-3 mt-3">
                                                <select class="form-select" name="etapacontrol" id="etapacontrol">
                                                    <option disabled>Seleccione la etapa</option>
                                                    <option value="1" <?= ($control_actual == 1) ? 'selected' : ''; ?>>Diseño</option>
                                                    <option value="2" <?= ($control_actual == 2) ? 'selected' : ''; ?>>Revisión interna</option>
                                                    <option value="3" <?= ($control_actual == 3) ? 'selected' : ''; ?>>Revisión con cliente</option>
                                                    <option value="4" <?= ($control_actual == 4) ? 'selected' : ''; ?>>Diagramas</option>
                                                    <option value="5" <?= ($control_actual == 5) ? 'selected' : ''; ?>>Bom</option>
                                                    <option value="6" <?= ($control_actual == 6) ? 'selected' : ''; ?>>Manufactura</option>
                                                    <option value="7" <?= ($control_actual == 7) ? 'selected' : ''; ?>>Programación</option>
                                                    <option value="8" <?= ($control_actual == 8) ? 'selected' : ''; ?>>Debugging</option>
                                                    <option value="9" <?= ($control_actual == 9) ? 'selected' : ''; ?>>Documentación</option>
                                                </select>
                                                    <label for="etapacontrol">Etapa de control</label>
                                                </div>

                                                <div class="form-floating col-6 mt-3">
                                                <select class="form-select" name="etapamecanica" id="etapamecanica">
                                                    <option disabled>Seleccione la etapa</option>
                                                    <option value="1" <?= ($mecanica_actual == 1) ? 'selected' : ''; ?>>Revisión BOM mecánico</option>
                                                    <option value="2" <?= ($mecanica_actual == 2) ? 'selected' : ''; ?>>Armado de componentes mecánicos</option>
                                                    <option value="3" <?= ($contromecanica_actuall_mecanica_actualactual == 3) ? 'selected' : ''; ?>>Pruebas de ensamble</option>
                                                    <option value="4" <?= ($mecanica_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                    <option value="5" <?= ($mecanica_actual == 5) ? 'selected' : ''; ?>>Desensamble para acabados</option>
                                                    <option value="6" <?= ($mecanica_actual == 6) ? 'selected' : ''; ?>>Armado final</option>
                                                </select>
                                                    <label for="etapamecanica">Etapa de ensamble mecánica/neumatica</label>
                                                </div>

                                                <div class="form-floating col-6 mt-3">
                                                <select class="form-select" name="etapatcontrol" id="etapatcontrol">
                                                    <option disabled>Seleccione la etapa</option>
                                                    <option value="1" <?= ($tcontrol_actual == 1) ? 'selected' : ''; ?>>Revisión BOM controles</option>
                                                    <option value="2" <?= ($tcontrol_actual == 2) ? 'selected' : ''; ?>>Armado de tableros de control</option>
                                                    <option value="3" <?= ($tcontrol_actual == 3) ? 'selected' : ''; ?>>Pruebas electrícas y de comunicación</option>
                                                    <option value="4" <?= ($tcontrol_actual == 4) ? 'selected' : ''; ?>>Remediación</option>
                                                    <option value="5" <?= ($tcontrol_actual == 5) ? 'selected' : ''; ?>>Ensamble en maquinaria</option>
                                                    <option value="6" <?= ($tcontrol_actual == 6) ? 'selected' : ''; ?>>Ruteo final</option>
                                                    <option value="7" <?= ($tcontrol_actual == 7) ? 'selected' : ''; ?>>Etiquetado</option>
                                                </select>
                                                    <label for="etapatcontrol">Etapa ensamble T.Control</label>
                                                </div>

                                                <div class="form-floating col-12 mt-3">
                                                    <textarea type="text" class="form-control" name="detalles" id="detalles" style="min-height:150px;"><?= $registro['detalles']; ?></textarea>
                                                    <label for="detalles">Detalles</label>
                                                </div>

                                                <div class="col-12 text-center mt-3">
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        Actualizar proyecto
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO USUARIO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeusuarios.php" method="POST">
                        <div class="row">
                            <div class="col-12 mtop">
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" autocomplete="off" required>
                            </div>

                            <div class="col-6 mtop">
                                <input type="text" class="form-control" name="apellidop" id="apellidop" placeholder="Apellido paterno" autocomplete="off" required>
                            </div>

                            <div class="col-6 mtop">
                                <input type="text" class="form-control" name="apellidom" id="apellidom" placeholder="Apellido materno" autocomplete="off" required>
                            </div>

                            <div class="col-5 mtop">
                                <input type="text" class="form-control" name="username" id="username" placeholder="Nombre de usuario" autocomplete="off" required>
                            </div>

                            <div class="col-7 mtop">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" required>
                            </div>

                            <div class="col-12 mtop">
                                <select class="form-select" name="rol" id="rol" autocomplete="off" required>
                                    <option disabled>Categoría</option>
                                    <option value="1">Bachillerato</option>
                                    <option value="2">Licenciatura escolarizada</option>
                                    <option value="3">Licenciatura ejecutiva</option>
                                    <option value="4">Administrador</option>
                                    <option value="5">Control escolar</option>
                                </select>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>